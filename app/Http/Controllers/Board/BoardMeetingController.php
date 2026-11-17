<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardMeetingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $memberId = $request->user('board')->id;
        $meetings = DB::table('board_meetings as m')
            ->leftJoin('board_meeting_rsvps as r', function ($join) use ($memberId): void {
                $join->on('r.meeting_id', '=', 'm.id')->where('r.member_id', '=', $memberId);
            })
            ->where('m.ends_at', '>=', now()->subDay())
            ->select('m.id', 'm.title', 'm.description', 'm.location', 'm.starts_at', 'm.ends_at', 'r.response as my_rsvp')
            ->selectSub(function ($query): void {
                $query->from('board_meeting_rsvps')->selectRaw('count(*)')
                    ->whereColumn('meeting_id', 'm.id')->where('response', 'going');
            }, 'attending_count')
            ->orderBy('m.starts_at')
            ->paginate(30);

        return response()->json($meetings);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user('board')->role, ['admin', 'super_admin'], true), 403);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);
        $id = DB::table('board_meetings')->insertGetId([
            ...$data,
            'created_by' => $request->user('board')->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $members = DB::table('board_members')->where('status', 'active')
            ->where('id', '!=', $request->user('board')->id)->pluck('id');
        foreach ($members as $memberId) {
            DB::table('board_notifications')->insert([
                'member_id' => $memberId, 'category' => 'meetings',
                'title' => 'New Board meeting: '.$data['title'],
                'body' => $data['location'] ?? null, 'url' => '/?section=meetings',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        return response()->json(['id' => $id], 201);
    }

    public function rsvp(Request $request, int $meeting): JsonResponse
    {
        $data = $request->validate(['response' => ['required', Rule::in(['going', 'maybe', 'declined'])]]);
        abort_unless(DB::table('board_meetings')->where('id', $meeting)->where('ends_at', '>', now())->exists(), 404);
        DB::table('board_meeting_rsvps')->updateOrInsert(
            ['meeting_id' => $meeting, 'member_id' => $request->user('board')->id],
            ['response' => $data['response'], 'created_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['saved' => true]);
    }
}
