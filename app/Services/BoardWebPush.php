<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;
use RuntimeException;

class BoardWebPush
{
    public function deliver(int $memberId, string $category, string $title, ?string $body, string $url): void
    {
        $public = config('services.webpush.public_key');
        $private = config('services.webpush.private_key');
        if (! $public || ! $private) {
            return;
        }

        // Do not send alerts to members whose Board access was revoked.
        if (! DB::table('board_members')->where('id', $memberId)->where('status', 'active')->exists()) {
            return;
        }

        $subscriptions = DB::table('board_push_subscriptions')
            ->where('member_id', $memberId)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => $public,
                'privateKey' => $private,
            ],
        ]);
        $payload = json_encode([
            'title' => mb_strimwidth($title, 0, 120, '…'),
            'body' => mb_strimwidth($body ?? '', 0, 280, '…'),
            'url' => str_starts_with($url, '/') && ! str_starts_with($url, '//') ? $url : '/',
            'tag' => 'board-'. $category .'-'.bin2hex(random_bytes(4)),
        ], JSON_THROW_ON_ERROR);

        $failed = false;
        foreach ($subscriptions as $device) {
            $preferences = json_decode($device->preferences ?? '{}', true) ?: [];
            if (($preferences[$category] ?? true) === false) {
                continue;
            }
            try {
                $webPush->queueNotification(Subscription::create([
                    'endpoint' => $device->endpoint,
                    'publicKey' => $device->p256dh,
                    'authToken' => $device->auth,
                    'contentEncoding' => 'aes128gcm',
                ]), $payload);
            } catch (Throwable $exception) {
                $failed = true;
                Log::warning('Board push subscription could not be queued', [
                    'subscription_id' => $device->id, 'error' => $exception->getMessage(),
                ]);
            }
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                continue;
            }
            $endpoint = $report->getEndpoint();
            if ($report->isSubscriptionExpired()) {
                DB::table('board_push_subscriptions')
                    ->where('member_id', $memberId)
                    ->where('endpoint_hash', hash('sha256', $endpoint))->delete();
            } else {
                $failed = true;
                Log::warning('Board push delivery failed', [
                    'endpoint_hash' => hash('sha256', $endpoint),
                    'reason' => $report->getReason(),
                ]);
            }
        }
        if ($failed) {
            throw new RuntimeException('One or more Board push endpoints could not be reached.');
        }
    }
}
