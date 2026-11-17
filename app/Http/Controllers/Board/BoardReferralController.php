<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardReferralController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $memberId = $request->user('board')->id;
        return response()->json(DB::table('board_referrals as r')
            ->join('board_members as sender', 'sender.id', '=', 'r.from_member_id')
            ->join('board_members as recipient', 'recipient.id', '=', 'r.to_member_id')
            ->where(function ($query) use ($memberId): void {
                $query->where('r.from_member_id', $memberId)->orWhere('r.to_member_id', $memberId);
            })
            ->select('r.id', 'r.subject', 'r.details', 'r.status', 'r.from_member_id', 'r.to_member_id', 'r.created_at', 'sender.name as from_name', 'recipient.name as to_name')
            ->orderByDesc('r.id')->paginate(30));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to_member_id' => ['required', 'integer', 'exists:board_members,id'],
            'subject' => ['required', 'string', 'max:180'],
            'details' => ['nullable', 'string', 'max:3000'],
        ]);
        $memberId = $request->user('board')->id;
        abort_if((int) $data['to_member_id'] === $memberId, 422, 'Choose another member.');
        abort_unless(DB::table('board_members')->where('id', $data['to_member_id'])->where('status', 'active')->exists(), 422);

        $id = DB::table('board_referrals')->insertGetId([
            ...$data, 'from_member_id' => $memberId, 'status' => 'new',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('board_notifications')->insert([
            'member_id' => $data['to_member_id'], 'category' => 'referrals',
            'title' => 'New referral from '.$request->user('board')->name,
            'body' => $data['subject'], 'url' => '/?section=referrals',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id], 201);
    }

    public function update(Request $request, int $referral): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['accepted', 'completed', 'declined'])]]);
        $updated = DB::table('board_referrals')
            ->where('id', $referral)
            ->where('to_member_id', $request->user('board')->id)
            ->update(['status' => $data['status'], 'updated_at' => now()]);
        abort_unless($updated, 404);
        return response()->json(['saved' => true]);
    }
}
