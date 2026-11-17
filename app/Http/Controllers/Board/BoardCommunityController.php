<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardCommunityController extends Controller
{
    public function directory(Request $request): JsonResponse
    {
        $members = DB::table('board_members')->where('status', 'active')
            ->select('id', 'name', 'company', 'industry', 'title', 'website', 'bio')
            ->orderBy('name')->paginate(30);

        return response()->json($members);
    }

    public function conversations(Request $request): JsonResponse
    {
        $memberId = $request->user('board')->id;
        $conversations = DB::table('board_conversations as c')
            ->join('board_conversation_members as cm', 'cm.conversation_id', '=', 'c.id')
            ->where('cm.member_id', $memberId)
            ->select('c.id', 'c.name', 'c.type', 'c.updated_at')
            ->selectSub(function ($query) use ($memberId): void {
                $query->from('board_messages as msg')
                    ->whereColumn('msg.conversation_id', 'c.id')
                    ->where('msg.sender_id', '!=', $memberId)
                    ->where(function ($query): void {
                        $query->whereNull('cm.last_read_at')
                            ->orWhereColumn('msg.created_at', '>', 'cm.last_read_at');
                    })
                    ->selectRaw('count(*)');
            }, 'unread_count')
            ->orderByDesc('c.updated_at')->paginate(30);

        return response()->json($conversations);
    }

    public function createConversation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:group,direct'],
            'name' => ['required_if:type,group', 'nullable', 'string', 'max:120'],
            'member_ids' => ['required', 'array', 'min:1', 'max:50'],
            'member_ids.*' => ['required', 'integer', 'distinct', 'exists:board_members,id'],
        ]);

        $creatorId = $request->user('board')->id;
        $participantIds = array_values(array_unique(array_merge([$creatorId], $data['member_ids'])));
        abort_unless(DB::table('board_members')->whereIn('id', $participantIds)
            ->where('status', 'active')->count() === count($participantIds), 422);

        if ($data['type'] === 'direct') {
            abort_unless(count($participantIds) === 2, 422, 'Direct messages require two members.');
        }

        $conversationId = DB::transaction(function () use ($data, $creatorId, $participantIds): int {
            $id = DB::table('board_conversations')->insertGetId([
                'name' => $data['type'] === 'group' ? $data['name'] : null,
                'type' => $data['type'],
                'created_by' => $creatorId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('board_conversation_members')->insert(array_map(fn (int $memberId): array => [
                'conversation_id' => $id,
                'member_id' => $memberId,
                'created_at' => now(),
                'updated_at' => now(),
            ], $participantIds));

            return $id;
        });

        return response()->json(['id' => $conversationId], 201);
    }

    public function messages(Request $request, int $conversation): JsonResponse
    {
        $this->assertParticipant($request, $conversation);

        DB::table('board_conversation_members')
            ->where('conversation_id', $conversation)
            ->where('member_id', $request->user('board')->id)
            ->update(['last_read_at' => now(), 'updated_at' => now()]);

        return response()->json(DB::table('board_messages')
            ->where('conversation_id', $conversation)
            ->select('id', 'sender_id', 'body', 'created_at')
            ->orderByDesc('id')->paginate(50));
    }

    public function send(Request $request, int $conversation): JsonResponse
    {
        $this->assertParticipant($request, $conversation);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $id = DB::table('board_messages')->insertGetId([
            'conversation_id' => $conversation,
            'sender_id' => $request->user('board')->id,
            'body' => $data['body'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('board_conversations')->where('id', $conversation)->update(['updated_at' => now()]);
        $recipients = DB::table('board_conversation_members')->where('conversation_id', $conversation)
            ->where('member_id', '!=', $request->user('board')->id)->pluck('member_id');
        foreach ($recipients as $recipientId) {
            DB::table('board_notifications')->insert([
                'member_id' => $recipientId, 'category' => 'messages',
                'title' => 'New message from '.$request->user('board')->name,
                'body' => mb_strimwidth($data['body'], 0, 280, '…'),
                'url' => '/?conversation='.$conversation,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return response()->json(['id' => $id], 201);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'bio' => ['nullable', 'string', 'max:3000'],
        ]);
        $request->user('board')->update($data);

        return response()->json(['saved' => true]);
    }

    private function assertParticipant(Request $request, int $conversation): void
    {
        abort_unless(DB::table('board_conversation_members')
            ->where('conversation_id', $conversation)
            ->where('member_id', $request->user('board')->id)
            ->exists(), 404);
    }
}
