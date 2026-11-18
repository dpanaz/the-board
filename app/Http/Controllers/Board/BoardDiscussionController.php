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
    private const VOTES = ['do_it', 'dont_do_it', 'need_more_information'];

    private function isAdmin(Request $request): bool
    {
        return in_array($request->user('board')->role, ['admin', 'super_admin'], true);
    }

    private function discussionQuery(Request $request)
    {
        $viewer = (int) $request->user('board')->id;
        $admin = $this->isAdmin($request);
        return DB::table('board_discussions as d')
            ->join('board_members as m', 'm.id', '=', 'd.member_id')
            ->where('m.status', 'active')
            ->select('d.id', 'd.title', 'd.category', 'd.body', 'd.outcome', 'd.resolved_at', 'd.created_at', 'd.is_anonymous')
            ->selectRaw('CASE WHEN d.is_anonymous = 0 OR d.member_id = ? OR ? = 1 THEN d.member_id ELSE NULL END as member_id', [$viewer, $admin ? 1 : 0])
            ->selectRaw("CASE WHEN d.is_anonymous = 0 OR d.member_id = ? OR ? = 1 THEN m.name ELSE 'Anonymous member' END as author", [$viewer, $admin ? 1 : 0])
            ->selectSub(function ($q): void {
                $q->from('board_discussion_replies')->selectRaw('count(*)')->whereColumn('discussion_id', 'd.id');
            }, 'reply_count')
            ->selectSub(function ($q) use ($viewer): void {
                $q->from('board_discussion_votes')->select('choice')
                    ->whereColumn('discussion_id', 'd.id')->where('member_id', $viewer)->limit(1);
            }, 'my_vote');
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->discussionQuery($request);
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
            'is_anonymous' => ['sometimes', 'boolean'],
        ]);
        $id = DB::table('board_discussions')->insertGetId([
            ...$data, 'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'member_id' => $request->user('board')->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id], 201);
    }

    public function show(Request $request, int $discussion): JsonResponse
    {
        $post = $this->discussionQuery($request)->where('d.id', $discussion)->first();
        abort_unless($post, 404);
        $replies = DB::table('board_discussion_replies as r')
            ->join('board_members as m', 'm.id', '=', 'r.member_id')
            ->where('r.discussion_id', $discussion)->where('m.status', 'active')
            ->select('r.id', 'r.member_id', 'm.name as author', 'r.body', 'r.created_at')
            ->orderBy('r.created_at')->limit(200)->get();
        $votes = DB::table('board_discussion_votes')->where('discussion_id', $discussion)
            ->select('choice')->selectRaw('count(*) as total')->groupBy('choice')->pluck('total', 'choice');
        return response()->json(['discussion' => $post, 'replies' => $replies, 'votes' => [
            'do_it' => (int) ($votes['do_it'] ?? 0),
            'dont_do_it' => (int) ($votes['dont_do_it'] ?? 0),
            'need_more_information' => (int) ($votes['need_more_information'] ?? 0),
        ]]);
    }

    public function reply(Request $request, int $discussion): JsonResponse
    {
        $post = DB::table('board_discussions')->where('id', $discussion)->first();
        abort_unless($post, 404);
        $data = $request->validate(['body' => ['required', 'string', 'min:2', 'max:5000']]);
        $senderId = (int) $request->user('board')->id;
        $id = DB::transaction(function () use ($discussion, $post, $data, $senderId): int {
            $id = DB::table('board_discussion_replies')->insertGetId([
                'discussion_id' => $discussion, 'member_id' => $senderId,
                'body' => $data['body'], 'created_at' => now(), 'updated_at' => now(),
            ]);
            $participants = DB::table('board_discussion_replies')->where('discussion_id', $discussion)
                ->where('member_id', '!=', $senderId)->distinct()->pluck('member_id')->all();
            $recipients = array_unique(array_merge([(int) $post->member_id], array_map('intval', $participants)));
            $active = DB::table('board_members')->whereIn('id', $recipients)
                ->where('status', 'active')->where('id', '!=', $senderId)->pluck('id');
            foreach ($active as $memberId) {
                DB::table('board_notifications')->insert([
                    'member_id' => $memberId, 'category' => 'messages',
                    'title' => 'New reply on The Sounding Board',
                    'body' => mb_strimwidth($post->title, 0, 240, '…'),
                    'url' => '/sounding-board#question-'.$discussion,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            return $id;
        });
        return response()->json(['id' => $id], 201);
    }

    public function vote(Request $request, int $discussion): JsonResponse
    {
        $post = DB::table('board_discussions')->where('id', $discussion)->first();
        abort_unless($post, 404);
        abort_unless($post->category === "Should I or Shouldn't I?", 422, 'Voting is available on decision questions only.');
        $data = $request->validate(['choice' => ['required', Rule::in(self::VOTES)]]);
        $memberId = (int) $request->user('board')->id;
        DB::table('board_discussion_votes')->upsert([[
            'discussion_id' => $discussion, 'member_id' => $memberId,
            'choice' => $data['choice'], 'created_at' => now(), 'updated_at' => now(),
        ]], ['discussion_id', 'member_id'], ['choice', 'updated_at']);
        return response()->json(['saved' => true]);
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
