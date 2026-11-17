<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendBoardMeetingReminders extends Command
{
    protected $signature = 'board:meeting-reminders';
    protected $description = 'Create 24-hour and 1-hour Board meeting reminders for attending members';

    public function handle(): int
    {
        $count = 0;
        foreach ([1440, 60] as $minutes) {
            $windowStart = now()->addMinutes($minutes);
            $windowEnd = $windowStart->copy()->addMinutes(5);
            $meetings = DB::table('board_meetings as m')
                ->join('board_meeting_rsvps as r', 'r.meeting_id', '=', 'm.id')
                ->join('board_members as bm', 'bm.id', '=', 'r.member_id')
                ->where('r.response', 'going')->where('bm.status', 'active')
                ->where('m.starts_at', '>=', $windowStart)
                ->where('m.starts_at', '<', $windowEnd)
                ->select('m.id', 'm.title', 'm.starts_at', 'r.member_id')->get();

            foreach ($meetings as $meeting) {
                DB::transaction(function () use ($meeting, $minutes, &$count): void {
                    $inserted = DB::table('board_meeting_reminders')->insertOrIgnore([
                        'meeting_id' => $meeting->id,
                        'member_id' => $meeting->member_id,
                        'minutes_before' => $minutes,
                        'created_at' => now(),
                    ]);
                    if (! $inserted) {
                        return;
                    }
                    DB::table('board_notifications')->insert([
                        'member_id' => $meeting->member_id,
                        'category' => 'meetings',
                        'title' => $minutes === 1440 ? 'Meeting tomorrow: '.$meeting->title : 'Meeting in one hour: '.$meeting->title,
                        'body' => 'Your RSVP is confirmed. See the meeting details in The Board.',
                        'url' => '/?section=meetings',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $count++;
                });
            }
        }
        $this->info("Created {$count} meeting reminders.");
        return self::SUCCESS;
    }
}
