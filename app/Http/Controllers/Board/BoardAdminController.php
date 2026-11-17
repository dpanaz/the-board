<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class BoardAdminController extends Controller
{
    public function invite(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['email' => ['required', 'email', 'max:255', 'unique:board_members,email']]);
        $token = Str::random(64);
        DB::table('board_invitations')->insert([
            'email' => strtolower($data['email']),
            'token_hash' => hash('sha256', $token),
            'invited_by' => $request->user('board')->id,
            'expires_at' => now()->addDays(7),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $url = route('board.join', ['token' => $token]);
        try {
            Mail::raw(
                "You have been invited to join The Board, a private community of local business owners hosted by The Clubhouse.\n\nAccept your invitation: {$url}\n\nThis link expires in seven days. Your membership requires administrator approval.",
                function ($message) use ($data): void {
                    $message->to($data['email'])->subject('Your invitation to The Board');
                }
            );
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'invitation_url' => $url,
                'email_sent' => false,
                'message' => 'Invitation created, but the email could not be sent. Share the link securely.',
            ], 201);
        }

        return response()->json(['invitation_url' => $url, 'email_sent' => true], 201);
    }

    public function pending(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        return response()->json(DB::table('board_members')->where('status', 'pending')
            ->select('id', 'name', 'email', 'company', 'created_at')->paginate(30));
    }

    public function decision(Request $request, int $member): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['status' => ['required', 'in:active,rejected,suspended']]);
        $updated = DB::table('board_members')->where('id', $member)
            ->where('role', 'member')->update(['status' => $data['status'], 'updated_at' => now()]);
        abort_unless($updated, 404);

        return response()->json(['saved' => true]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array($request->user('board')->role, ['admin', 'super_admin'], true), 403);
    }
}
