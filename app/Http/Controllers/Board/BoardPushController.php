<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardPushController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2048', 'starts_with:https://'],
            'keys.p256dh' => ['required', 'string', 'max:512'],
            'keys.auth' => ['required', 'string', 'max:512'],
        ]);

        $endpoint = $data['endpoint'];
        $hash = hash('sha256', $endpoint);

        $existing = DB::table('board_push_subscriptions')->where('endpoint_hash', $hash)->first();
        abort_if($existing && (int) $existing->member_id !== (int) $request->user('board')->id, 409, 'This device is already linked to another member.');

        DB::table('board_push_subscriptions')->updateOrInsert(
            ['endpoint_hash' => $hash],
            [
                'member_id' => $request->user('board')->id,
                'endpoint' => $endpoint,
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
                'created_at' => $existing?->created_at ?? now(),
                'updated_at' => now(),
            ]
        );

        return response()->json(['subscribed' => true]);
    }

    public function preferences(Request $request): JsonResponse
    {
        $subscriptions = DB::table('board_push_subscriptions')
            ->where('member_id', $request->user('board')->id)
            ->select('endpoint_hash', 'preferences', 'created_at')->get()
            ->map(fn ($subscription): array => [
                'device' => substr($subscription->endpoint_hash, 0, 12),
                'preferences' => json_decode($subscription->preferences ?? '{}', true) ?: [],
                'subscribed_at' => $subscription->created_at,
            ]);

        return response()->json(['devices' => $subscriptions]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'url', 'max:2048', 'starts_with:https://'],
            'messages' => ['required', 'boolean'],
            'meetings' => ['required', 'boolean'],
            'referrals' => ['required', 'boolean'],
        ]);
        $updated = DB::table('board_push_subscriptions')
            ->where('member_id', $request->user('board')->id)
            ->where('endpoint_hash', hash('sha256', $data['endpoint']))
            ->update(['preferences' => json_encode([
                'messages' => $data['messages'],
                'meetings' => $data['meetings'],
                'referrals' => $data['referrals'],
            ]), 'updated_at' => now()]);
        abort_unless($updated, 404);

        return response()->json(['saved' => true]);
    }

    public function testStatus(Request $request): JsonResponse
    {
        $memberId = $request->user('board')->id;
        $latest = DB::table('board_notifications')
            ->where('member_id', $memberId)
            ->where('title', 'The Board notification test')
            ->orderByDesc('id')->first();

        return response()->json([
            'configured' => (bool) (config('services.webpush.public_key') && config('services.webpush.private_key')),
            'device_count' => DB::table('board_push_subscriptions')->where('member_id', $memberId)->count(),
            'latest_test' => $latest ? [
                'queued_at' => $latest->created_at,
                'processed_at' => $latest->push_processed_at,
                'attempts' => (int) $latest->push_attempts,
                'next_attempt_at' => $latest->push_next_attempt_at,
                'error' => $latest->push_last_error !== null,
            ] : null,
        ]);
    }

    public function testNotification(Request $request): JsonResponse
    {
        $member = $request->user('board');
        DB::table('board_notifications')->insert([
            'member_id' => $member->id,
            'category' => 'messages',
            'title' => 'The Board notification test',
            'body' => 'If this appears on your phone, push notifications are working.',
            'url' => '/',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return response()->json(['queued' => true, 'message' => 'Test notification added to your activity inbox and push queue.']);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate(['endpoint' => ['required', 'url', 'max:2048']]);
        DB::table('board_push_subscriptions')
            ->where('member_id', $request->user('board')->id)
            ->where('endpoint_hash', hash('sha256', $data['endpoint']))
            ->delete();

        return response()->json(['subscribed' => false]);
    }
}
