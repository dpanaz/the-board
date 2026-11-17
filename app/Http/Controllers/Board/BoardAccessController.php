<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BoardAccessController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::guard('board')->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }

        if (! $request->user('board')->isApproved()) {
            Auth::guard('board')->logout();
            $request->session()->regenerate();
            throw ValidationException::withMessages(['email' => 'Your Board account is awaiting approval or is inactive.']);
        }

        $request->session()->regenerate();

        return redirect()->route('board.home');
    }

    public function accept(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        DB::transaction(function () use ($data): void {
            $invitation = DB::table('board_invitations')
                ->where('token_hash', hash('sha256', $data['token']))
                ->lockForUpdate()->first();

            if (! $invitation || $invitation->accepted_at || now()->greaterThan($invitation->expires_at)) {
                throw ValidationException::withMessages(['token' => 'This invitation is invalid or expired.']);
            }

            BoardMember::create([
                'email' => $invitation->email,
                'name' => $data['name'],
                'password' => $data['password'],
            ]);

            DB::table('board_invitations')->where('id', $invitation->id)->update(['accepted_at' => now()]);
        });

        return redirect()->route('board.login')->with('status', 'Registration submitted. An administrator must approve your membership.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('board')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('board.login');
    }
}
