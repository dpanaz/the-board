<?php

namespace App\Console\Commands;

use App\Services\BoardWebPush;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendBoardPushNotifications extends Command
{
    protected $signature = 'board:send-push';
    protected $description = 'Deliver pending Board activity to opted-in mobile devices';

    public function handle(BoardWebPush $push): int
    {
        if (! config('services.webpush.public_key') || ! config('services.webpush.private_key')) {
            $this->warn('Board VAPID credentials are not configured; leaving notifications pending.');
            return self::SUCCESS;
        }

        $notifications = DB::table('board_notifications')
            ->whereNull('push_processed_at')
            ->where(function ($query): void {
                $query->whereNull('push_next_attempt_at')->orWhere('push_next_attempt_at', '<=', now());
            })
            ->orderBy('id')->limit(100)->get();

        foreach ($notifications as $notification) {
            try {
                $push->deliver(
                    (int) $notification->member_id,
                    $notification->category,
                    $notification->title,
                    $notification->body,
                    $notification->url
                );
                DB::table('board_notifications')->where('id', $notification->id)
                    ->whereNull('push_processed_at')->update([
                        'push_processed_at' => now(), 'push_last_error' => null,
                        'push_next_attempt_at' => null,
                    ]);
            } catch (Throwable $exception) {
                report($exception);
                $attempts = (int) $notification->push_attempts + 1;
                $delayMinutes = min(60, 2 ** min($attempts, 6));
                DB::table('board_notifications')->where('id', $notification->id)
                    ->whereNull('push_processed_at')->update([
                        'push_attempts' => $attempts,
                        'push_next_attempt_at' => $attempts >= 5 ? null : now()->addMinutes($delayMinutes),
                        'push_processed_at' => $attempts >= 5 ? now() : null,
                        'push_last_error' => mb_strimwidth($exception->getMessage(), 0, 1000),
                    ]);
                $this->error('Could not deliver Board notification '.$notification->id.' (attempt '.$attempts.').');
            }
        }

        $this->info('Processed '.count($notifications).' Board notifications.');
        return self::SUCCESS;
    }
}
