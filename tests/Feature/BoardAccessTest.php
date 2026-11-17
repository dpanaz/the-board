<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BoardAccessTest extends TestCase
{
    use RefreshDatabase;

    private function boardUrl(string $path): string
    {
        return 'https://theboard.clubhouseoncastell.com'.$path;
    }

    public function test_uninvited_registration_is_rejected(): void
    {
        $this->post($this->boardUrl('/join'), [
            'token' => 'invalid',
            'name' => 'Visitor',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertSessionHasErrors('token');

        $this->assertDatabaseCount('board_members', 0);
    }

    public function test_invited_registration_requires_approval(): void
    {
        $token = Str::random(64);
        DB::table('board_invitations')->insert([
            'email' => 'owner@example.test',
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->post($this->boardUrl('/join'), [
            'token' => $token,
            'name' => 'Business Owner',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertRedirect();

        $member = BoardMember::where('email', 'owner@example.test')->firstOrFail();
        $this->assertSame('pending', $member->status);

        $this->actingAs($member, 'board')
            ->get($this->boardUrl('/'))
            ->assertForbidden();
    }

    public function test_board_members_cannot_access_another_conversation(): void
    {
        $member = BoardMember::create([
            'name' => 'Approved Owner', 'email' => 'approved@example.test',
            'password' => 'SecurePassword123!',
        ]);
        $member->forceFill(['status' => 'active'])->save();

        $conversationId = DB::table('board_conversations')->insertGetId([
            'name' => 'Private Conversation', 'type' => 'direct',
            'created_by' => $member->id, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($member, 'board')
            ->get($this->boardUrl('/api/conversations/'.$conversationId.'/messages'))
            ->assertNotFound();
    }
    public function test_approved_members_can_create_a_private_conversation(): void
    {
        $first = BoardMember::create(['name' => 'First', 'email' => 'first@example.test', 'password' => 'SecurePassword123!']);
        $second = BoardMember::create(['name' => 'Second', 'email' => 'second@example.test', 'password' => 'SecurePassword123!']);
        $first->forceFill(['status' => 'active'])->save();
        $second->forceFill(['status' => 'active'])->save();

        $this->actingAs($first, 'board')
            ->postJson($this->boardUrl('/api/conversations'), [
                'type' => 'direct', 'member_ids' => [$second->id],
            ])->assertCreated();

        $this->assertDatabaseCount('board_conversations', 1);
        $this->assertDatabaseCount('board_conversation_members', 2);
    }

    public function test_regular_members_cannot_approve_applicants(): void
    {
        $member = BoardMember::create(['name' => 'Member', 'email' => 'member@example.test', 'password' => 'SecurePassword123!']);
        $applicant = BoardMember::create(['name' => 'Applicant', 'email' => 'applicant@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();

        $this->actingAs($member, 'board')
            ->patchJson($this->boardUrl('/api/admin/members/'.$applicant->id), ['status' => 'active'])
            ->assertForbidden();

        $this->assertSame('pending', $applicant->fresh()->status);
    }

    public function test_regular_members_cannot_schedule_meetings(): void
    {
        $member = BoardMember::create(['name' => 'Member', 'email' => 'meeting-member@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();

        $this->actingAs($member, 'board')->postJson($this->boardUrl('/api/meetings'), [
            'title' => 'Business Breakfast',
            'starts_at' => now()->addDay()->toIso8601String(),
            'ends_at' => now()->addDay()->addHour()->toIso8601String(),
        ])->assertForbidden();
    }

    public function test_admin_can_schedule_meeting_and_member_can_rsvp(): void
    {
        $admin = BoardMember::create(['name' => 'Admin', 'email' => 'meeting-admin@example.test', 'password' => 'SecurePassword123!']);
        $admin->forceFill(['status' => 'active', 'role' => 'admin'])->save();
        $member = BoardMember::create(['name' => 'Member', 'email' => 'attendee@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();

        $response = $this->actingAs($admin, 'board')->postJson($this->boardUrl('/api/meetings'), [
            'title' => 'Business Breakfast',
            'location' => 'The Clubhouse',
            'starts_at' => now()->addDay()->toIso8601String(),
            'ends_at' => now()->addDay()->addHour()->toIso8601String(),
        ])->assertCreated();

        $meetingId = $response->json('id');
        $this->actingAs($member, 'board')->putJson($this->boardUrl('/api/meetings/'.$meetingId.'/rsvp'), [
            'response' => 'going',
        ])->assertOk();

        $this->assertDatabaseHas('board_meeting_rsvps', [
            'meeting_id' => $meetingId,
            'member_id' => $member->id,
            'response' => 'going',
        ]);
    }

    public function test_unread_messages_are_counted_and_cleared_when_opened(): void
    {
        $first = BoardMember::create(['name' => 'First', 'email' => 'unread-first@example.test', 'password' => 'SecurePassword123!']);
        $second = BoardMember::create(['name' => 'Second', 'email' => 'unread-second@example.test', 'password' => 'SecurePassword123!']);
        $first->forceFill(['status' => 'active'])->save();
        $second->forceFill(['status' => 'active'])->save();

        $conversation = DB::table('board_conversations')->insertGetId([
            'name' => 'Discussion', 'type' => 'direct', 'created_by' => $first->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach ([$first, $second] as $member) {
            DB::table('board_conversation_members')->insert([
                'conversation_id' => $conversation, 'member_id' => $member->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->actingAs($first, 'board')->postJson($this->boardUrl('/api/conversations/'.$conversation.'/messages'), [
            'body' => 'Hello!',
        ])->assertCreated();

        $this->actingAs($second, 'board')->getJson($this->boardUrl('/api/conversations'))
            ->assertOk()->assertJsonPath('data.0.unread_count', 1);
        $this->getJson($this->boardUrl('/api/conversations/'.$conversation.'/messages'))->assertOk();
        $this->getJson($this->boardUrl('/api/conversations'))
            ->assertOk()->assertJsonPath('data.0.unread_count', 0);
    }

    public function test_approved_member_can_subscribe_and_unsubscribe_device(): void
    {
        $member = BoardMember::create(['name' => 'Push Member', 'email' => 'push-member@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();
        $endpoint = 'https://push.example.test/subscription/123';

        $this->actingAs($member, 'board')->postJson($this->boardUrl('/api/push/subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'public-device-key', 'auth' => 'auth-secret'],
        ])->assertOk();

        $this->assertDatabaseHas('board_push_subscriptions', [
            'member_id' => $member->id,
            'endpoint_hash' => hash('sha256', $endpoint),
        ]);

        $this->postJson($this->boardUrl('/api/push/unsubscribe'), [
            'endpoint' => $endpoint,
        ])->assertOk();

        $this->assertDatabaseCount('board_push_subscriptions', 0);
    }

    public function test_referrals_are_private_and_only_recipient_can_update_status(): void
    {
        $sender = BoardMember::create(['name' => 'Sender', 'email' => 'ref-sender@example.test', 'password' => 'SecurePassword123!']);
        $recipient = BoardMember::create(['name' => 'Recipient', 'email' => 'ref-recipient@example.test', 'password' => 'SecurePassword123!']);
        $outsider = BoardMember::create(['name' => 'Outsider', 'email' => 'ref-outsider@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$sender, $recipient, $outsider] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }

        $response = $this->actingAs($sender, 'board')->postJson($this->boardUrl('/api/referrals'), [
            'to_member_id' => $recipient->id, 'subject' => 'Local introduction',
            'details' => 'Please connect with this business owner.',
        ])->assertCreated();
        $id = $response->json('id');

        $this->actingAs($outsider, 'board')->getJson($this->boardUrl('/api/referrals'))
            ->assertOk()->assertJsonCount(0, 'data');
        $this->patchJson($this->boardUrl('/api/referrals/'.$id), ['status' => 'completed'])
            ->assertNotFound();
        $this->actingAs($recipient, 'board')->patchJson($this->boardUrl('/api/referrals/'.$id), [
            'status' => 'accepted',
        ])->assertOk();
        $this->assertDatabaseHas('board_referrals', ['id' => $id, 'status' => 'accepted']);
    }

    public function test_member_can_only_update_own_device_notification_preferences(): void
    {
        $owner = BoardMember::create(['name' => 'Owner', 'email' => 'device-owner@example.test', 'password' => 'SecurePassword123!']);
        $other = BoardMember::create(['name' => 'Other', 'email' => 'device-other@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$owner, $other] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }
        $endpoint = 'https://push.example.test/subscription/owner';
        $this->actingAs($owner, 'board')->postJson($this->boardUrl('/api/push/subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'owner-device-key', 'auth' => 'owner-secret'],
        ])->assertOk();

        $this->actingAs($other, 'board')->patchJson($this->boardUrl('/api/push/preferences'), [
            'endpoint' => $endpoint, 'messages' => false, 'meetings' => true, 'referrals' => true,
        ])->assertNotFound();

        $this->actingAs($owner, 'board')->patchJson($this->boardUrl('/api/push/preferences'), [
            'endpoint' => $endpoint, 'messages' => false, 'meetings' => true, 'referrals' => false,
        ])->assertOk();

        $this->assertDatabaseHas('board_push_subscriptions', [
            'member_id' => $owner->id,
            'endpoint_hash' => hash('sha256', $endpoint),
        ]);
        $saved = DB::table('board_push_subscriptions')->where('member_id', $owner->id)->value('preferences');
        $this->assertEquals(['messages' => false, 'meetings' => true, 'referrals' => false], json_decode($saved, true));
    }

    public function test_activity_notifications_are_private_and_can_be_marked_read(): void
    {
        $sender = BoardMember::create(['name' => 'Activity Sender', 'email' => 'activity-sender@example.test', 'password' => 'SecurePassword123!']);
        $recipient = BoardMember::create(['name' => 'Activity Recipient', 'email' => 'activity-recipient@example.test', 'password' => 'SecurePassword123!']);
        $outsider = BoardMember::create(['name' => 'Activity Outsider', 'email' => 'activity-outsider@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$sender, $recipient, $outsider] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }
        $this->actingAs($sender, 'board')->postJson($this->boardUrl('/api/referrals'), [
            'to_member_id' => $recipient->id, 'subject' => 'A new opportunity',
        ])->assertCreated();
        $id = DB::table('board_notifications')->where('member_id', $recipient->id)->value('id');

        $this->actingAs($outsider, 'board')->getJson($this->boardUrl('/api/notifications'))
            ->assertOk()->assertJsonPath('unread_count', 0);
        $this->patchJson($this->boardUrl('/api/notifications/'.$id.'/read'))->assertNotFound();

        $this->actingAs($recipient, 'board')->getJson($this->boardUrl('/api/notifications'))
            ->assertOk()->assertJsonPath('unread_count', 1);
        $this->patchJson($this->boardUrl('/api/notifications/'.$id.'/read'))->assertOk();
        $this->getJson($this->boardUrl('/api/notifications'))
            ->assertOk()->assertJsonPath('unread_count', 0);
    }

    public function test_meeting_reminders_are_created_once_only_for_attending_members(): void
    {
        $admin = BoardMember::create(['name' => 'Reminder Admin', 'email' => 'reminder-admin@example.test', 'password' => 'SecurePassword123!']);
        $attendee = BoardMember::create(['name' => 'Reminder Attendee', 'email' => 'reminder-attendee@example.test', 'password' => 'SecurePassword123!']);
        $other = BoardMember::create(['name' => 'Reminder Other', 'email' => 'reminder-other@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$admin, $attendee, $other] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }
        $meeting = DB::table('board_meetings')->insertGetId([
            'title' => 'Referral roundtable', 'starts_at' => now()->addDay()->addMinutes(2),
            'ends_at' => now()->addDay()->addHours(2), 'created_by' => $admin->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach ([[$attendee->id, 'going'], [$other->id, 'maybe']] as [$id, $response]) {
            DB::table('board_meeting_rsvps')->insert([
                'meeting_id' => $meeting, 'member_id' => $id, 'response' => $response,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->artisan('board:meeting-reminders')->assertSuccessful();
        $this->artisan('board:meeting-reminders')->assertSuccessful();
        $this->assertDatabaseCount('board_meeting_reminders', 1);
        $this->assertDatabaseHas('board_notifications', [
            'member_id' => $attendee->id, 'category' => 'meetings',
            'title' => 'Meeting tomorrow: Referral roundtable',
        ]);
        $this->assertDatabaseMissing('board_notifications', [
            'member_id' => $other->id, 'category' => 'meetings',
        ]);
    }

    public function test_members_can_post_asks_and_only_close_their_own_posts(): void
    {
        $owner = BoardMember::create(['name' => 'Asking Owner', 'email' => 'asking-owner@example.test', 'password' => 'SecurePassword123!']);
        $other = BoardMember::create(['name' => 'Offering Owner', 'email' => 'offering-owner@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$owner, $other] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }

        $id = $this->actingAs($owner, 'board')->postJson($this->boardUrl('/api/opportunities'), [
            'type' => 'ask', 'title' => 'Looking for a bookkeeper',
            'details' => 'Seeking recommendations from local owners.',
        ])->assertCreated()->json('id');

        $this->actingAs($other, 'board')->getJson($this->boardUrl('/api/opportunities?type=ask'))
            ->assertOk()->assertJsonCount(1, 'data');
        $this->patchJson($this->boardUrl('/api/opportunities/'.$id.'/close'))->assertNotFound();

        $this->actingAs($owner, 'board')->patchJson($this->boardUrl('/api/opportunities/'.$id.'/close'))
            ->assertOk();
        $this->getJson($this->boardUrl('/api/opportunities'))->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_a_push_subscription_cannot_be_taken_over_by_another_member(): void
    {
        $owner = BoardMember::create(['name' => 'Push Owner', 'email' => 'push-owner@example.test', 'password' => 'SecurePassword123!']);
        $other = BoardMember::create(['name' => 'Push Other', 'email' => 'push-other@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$owner, $other] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }
        $payload = [
            'endpoint' => 'https://push.example.test/owned-endpoint',
            'keys' => ['p256dh' => 'device-public-key', 'auth' => 'device-auth-key'],
        ];
        $this->actingAs($owner, 'board')->postJson($this->boardUrl('/api/push/subscribe'), $payload)
            ->assertOk();
        $this->actingAs($other, 'board')->postJson($this->boardUrl('/api/push/subscribe'), $payload)
            ->assertStatus(409);
        $this->assertDatabaseHas('board_push_subscriptions', [
            'endpoint_hash' => hash('sha256', $payload['endpoint']), 'member_id' => $owner->id,
        ]);
    }

    public function test_push_dispatch_leaves_notifications_pending_without_vapid_keys(): void
    {
        config()->set('services.webpush.public_key', null);
        config()->set('services.webpush.private_key', null);
        $member = BoardMember::create(['name' => 'Pending Push', 'email' => 'pending-push@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();
        $id = DB::table('board_notifications')->insertGetId([
            'member_id' => $member->id, 'category' => 'messages',
            'title' => 'New message', 'body' => 'Hello',
            'url' => '/', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->artisan('board:send-push')->assertSuccessful();
        $this->assertDatabaseHas('board_notifications', ['id' => $id, 'push_processed_at' => null]);
    }

    public function test_push_dispatch_marks_notifications_processed_when_sender_succeeds(): void
    {
        config()->set('services.webpush.public_key', 'test-public-key');
        config()->set('services.webpush.private_key', 'test-private-key');
        $member = BoardMember::create(['name' => 'Delivered Push', 'email' => 'delivered-push@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();
        $id = DB::table('board_notifications')->insertGetId([
            'member_id' => $member->id, 'category' => 'referrals',
            'title' => 'Referral', 'body' => 'An introduction',
            'url' => '/?section=referrals', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $sender = \Mockery::mock(\App\Services\BoardWebPush::class);
        $sender->shouldReceive('deliver')->once()->with(
            $member->id, 'referrals', 'Referral', 'An introduction', '/?section=referrals'
        );
        $this->app->instance(\App\Services\BoardWebPush::class, $sender);
        $this->artisan('board:send-push')->assertSuccessful();
        $this->assertDatabaseHas('board_notifications', ['id' => $id]);
        $this->assertNotNull(DB::table('board_notifications')->where('id', $id)->value('push_processed_at'));
    }

    public function test_push_dispatch_retries_a_transient_failure(): void
    {
        config()->set('services.webpush.public_key', 'test-public-key');
        config()->set('services.webpush.private_key', 'test-private-key');
        $member = BoardMember::create(['name' => 'Retry Member', 'email' => 'retry-member@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();
        $id = DB::table('board_notifications')->insertGetId([
            'member_id' => $member->id, 'category' => 'messages',
            'title' => 'New message', 'body' => 'Try again',
            'url' => '/', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $sender = \Mockery::mock(\App\Services\BoardWebPush::class);
        $sender->shouldReceive('deliver')->once()->andThrow(new \RuntimeException('Temporary push outage'));
        $this->app->instance(\App\Services\BoardWebPush::class, $sender);
        $this->artisan('board:send-push')->assertSuccessful();
        $row = DB::table('board_notifications')->where('id', $id)->first();
        $this->assertSame(1, (int) $row->push_attempts);
        $this->assertNull($row->push_processed_at);
        $this->assertNotNull($row->push_next_attempt_at);
        $this->artisan('board:send-push')->assertSuccessful();
    }

    public function test_revoked_members_do_not_receive_push_notifications(): void
    {
        config()->set('services.webpush.public_key', 'test-public-key');
        config()->set('services.webpush.private_key', 'test-private-key');
        $member = BoardMember::create(['name' => 'Revoked Member', 'email' => 'revoked-push@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'rejected'])->save();
        DB::table('board_push_subscriptions')->insert([
            'member_id' => $member->id,
            'endpoint' => 'https://push.example.test/revoked',
            'endpoint_hash' => hash('sha256', 'https://push.example.test/revoked'),
            'p256dh' => 'fake-key', 'auth' => 'fake-auth',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $sender = new \App\Services\BoardWebPush();
        $sender->deliver($member->id, 'messages', 'Private message', 'Sensitive details', '/');
        $this->assertDatabaseHas('board_push_subscriptions', ['member_id' => $member->id]);
    }

    public function test_member_can_queue_only_their_own_push_test(): void
    {
        $member = BoardMember::create(['name' => 'Test Alert Owner', 'email' => 'test-alert-owner@example.test', 'password' => 'SecurePassword123!']);
        $member->forceFill(['status' => 'active'])->save();
        $this->postJson($this->boardUrl('/api/push/test'))->assertUnauthorized();
        $this->actingAs($member, 'board')->postJson($this->boardUrl('/api/push/test'))
            ->assertOk()->assertJsonPath('queued', true);
        $this->assertDatabaseHas('board_notifications', [
            'member_id' => $member->id,
            'category' => 'messages',
            'title' => 'The Board notification test',
        ]);
    }

    public function test_members_only_see_their_own_push_test_status(): void
    {
        $first = BoardMember::create(['name' => 'First Tester', 'email' => 'first-tester@example.test', 'password' => 'SecurePassword123!']);
        $second = BoardMember::create(['name' => 'Second Tester', 'email' => 'second-tester@example.test', 'password' => 'SecurePassword123!']);
        foreach ([$first, $second] as $member) {
            $member->forceFill(['status' => 'active'])->save();
        }
        $this->actingAs($first, 'board')->postJson($this->boardUrl('/api/push/test'))->assertOk();
        $this->actingAs($second, 'board')->getJson($this->boardUrl('/api/push/test/status'))
            ->assertOk()->assertJsonPath('latest_test', null);
        $this->actingAs($first, 'board')->getJson($this->boardUrl('/api/push/test/status'))
            ->assertOk()->assertJsonPath('latest_test.attempts', 0);
    }


    public function test_clubhouse_host_does_not_serve_board_routes(): void
    {
        $this->get('https://clubhouseoncastell.com/login')->assertNotFound();
        $this->get('https://clubhouseoncastell.com/join')->assertNotFound();
        $this->get('https://clubhouseoncastell.com/api/members')->assertNotFound();
    }

    public function test_board_uses_only_its_own_authentication_guard(): void
    {
        $this->assertSame('board', config('auth.defaults.guard'));
        $this->assertArrayNotHasKey('web', config('auth.guards'));
        $this->assertArrayNotHasKey('users', config('auth.providers'));
        $this->assertSame('the_board_session', config('session.cookie'));
        $this->assertSame('theboard.clubhouseoncastell.com', config('session.domain'));
    }
}
