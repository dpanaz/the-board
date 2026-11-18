<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardDiscussionController extends Controller
{
    private const CATEGORIES = ['Should I or Shouldn\'t I?', 'What Do You Think?', 'Business Challenge', 'Looking for Ideas', 'Lessons Learned'];

    public function index(Request $request): JsonResponse
    {
        $query = DB::table('board_discussions as d')
            ->join('board_members as m', 'm.id', '=', 'd.member_id')
            ->where('m.status', 'active')
            ->select('d.id', 'd.member_id', 'm.name as author', 'd.title', 'd.category', 'd.body', 'd.outcome', 'd.resolved_at', 'd.created_at')
            ->selectSub(function ($q): void {
                $q->from('board_discussion_replies')->selectRaw('count(*)')->whereColumn('discussion_id', 'd.id');
            }, 'reply_count');
        if ($request->filled('category')) {
            $request->validate(['category' => [Rule::in(self::CATEGORIES)]]);
            $query->where('d.category', $request->query('category'));
        }
        return response()->json($query->orderByDesc('d.created_at')->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:180'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ]);
        $id = DB::table('board_discussions')->insertGetId([
            ...$data, 'member_id' => $request->user('board')->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id], 201);
    }

    public function show(Request $request, int $discussion): JsonResponse
    {
        $post = DB::table('board_discussions as d')
            ->join('board_members as m', 'm.id', '=', 'd.member_id')
            ->where('d.id', $discussion)->where('m.status', 'active')
            ->select('d.id', 'd.member_id', 'm.name as author', 'd.title', 'd.category', 'd.body', 'd.outcome', 'd.resolved_at', 'd.created_at')->first();
        abort_unless($post, 404);
        $replies = DB::table('board_discussion_replies as r')
            ->join('board_members as m', 'm.id', '=', 'r.member_id')
            ->where('r.discussion_id', $discussion)->where('m.status', 'active')
            ->select('r.id', 'r.member_id', 'm.name as author', 'r.body', 'r.created_at')
            ->orderBy('r.created_at')->limit(200)->get();
        return response()->json(['discussion' => $post, 'replies' => $replies]);
    }

    public function reply(Request $request, int $discussion): JsonResponse
    {
        abort_unless(DB::table('board_discussions')->where('id', $discussion)->exists(), 404);
        $data = $request->validate(['body' => ['required', 'string', 'min:2', 'max:5000']]);
        $id = DB::table('board_discussion_replies')->insertGetId([
            'discussion_id' => $discussion, 'member_id' => $request->user('board')->id,
            'body' => $data['body'], 'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id], 201);
    }

    public function outcome(Request $request, int $discussion): JsonResponse
    {
        $data = $request->validate(['outcome' => ['required', 'string', 'min:5', 'max:5000']]);
        $updated = DB::table('board_discussions')
            ->where('id', $discussion)->where('member_id', $request->user('board')->id)
            ->update(['outcome' => $data['outcome'], 'resolved_at' => now(), 'updated_at' => now()]);
        abort_unless($updated, 404);
        return response()->json(['saved' => true]);
    }
}
