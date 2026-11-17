<?php

use App\Http\Controllers\Board\BoardAccessController;
use App\Http\Controllers\Board\BoardCommunityController;
use App\Http\Controllers\Board\BoardAdminController;
use App\Http\Controllers\Board\BoardPushController;
use App\Http\Controllers\Board\BoardMeetingController;
use App\Http\Controllers\Board\BoardReferralController;
use App\Http\Controllers\Board\BoardNotificationController;
use App\Http\Controllers\Board\BoardOpportunityController;
use App\Http\Middleware\EnsureApprovedBoardMember;
use Illuminate\Support\Facades\Route;

Route::domain(config('services.board.domain', 'theboard.clubhouseoncastell.com'))->name('board.')->group(function (): void {
    Route::view('/login', 'board.login')->name('login')->middleware('guest:board');
    Route::post('/login', [BoardAccessController::class, 'login'])->middleware(['guest:board', 'throttle:5,1'])->name('login.submit');
    Route::view('/join', 'board.join')->name('join')->middleware('guest:board');
    Route::post('/join', [BoardAccessController::class, 'accept'])->middleware(['guest:board', 'throttle:5,1'])->name('join.submit');

    Route::middleware(['auth:board', EnsureApprovedBoardMember::class])->group(function (): void {
        Route::view('/', 'board.home')->name('home');
        Route::get('/admin', function (\Illuminate\Http\Request $request) {
            abort_unless(in_array($request->user('board')->role, ['admin', 'super_admin'], true), 403);
            return view('board.admin');
        })->name('admin');
        Route::get('/api/push/preferences', [BoardPushController::class, 'preferences'])->name('push.preferences');
        Route::patch('/api/push/preferences', [BoardPushController::class, 'updatePreferences'])->name('push.preferences.update');
        Route::post('/api/push/subscribe', [BoardPushController::class, 'subscribe'])->name('push.subscribe');
        Route::get('/api/push/test/status', [BoardPushController::class, 'testStatus'])->name('push.test.status');
        Route::post('/api/push/test', [BoardPushController::class, 'testNotification'])->middleware('throttle:3,1')->name('push.test');
        Route::post('/api/push/unsubscribe', [BoardPushController::class, 'unsubscribe'])->name('push.unsubscribe');
        Route::get('/api/opportunities', [BoardOpportunityController::class, 'index'])->name('opportunities');
        Route::post('/api/opportunities', [BoardOpportunityController::class, 'store'])->middleware('throttle:10,1')->name('opportunities.store');
        Route::patch('/api/opportunities/{opportunity}/close', [BoardOpportunityController::class, 'close'])->name('opportunities.close');
        Route::get('/api/notifications', [BoardNotificationController::class, 'index'])->name('notifications');
        Route::patch('/api/notifications/{notification}/read', [BoardNotificationController::class, 'markRead'])->name('notifications.read');
        Route::get('/api/referrals', [BoardReferralController::class, 'index'])->name('referrals');
        Route::post('/api/referrals', [BoardReferralController::class, 'store'])->middleware('throttle:10,1')->name('referrals.store');
        Route::patch('/api/referrals/{referral}', [BoardReferralController::class, 'update'])->name('referrals.update');
        Route::get('/api/meetings', [BoardMeetingController::class, 'index'])->name('meetings');
        Route::post('/api/meetings', [BoardMeetingController::class, 'store'])->middleware('throttle:10,1')->name('meetings.store');
        Route::put('/api/meetings/{meeting}/rsvp', [BoardMeetingController::class, 'rsvp'])->name('meetings.rsvp');
        Route::get('/api/members', [BoardCommunityController::class, 'directory'])->name('members');
        Route::patch('/api/profile', [BoardCommunityController::class, 'updateProfile'])->name('profile.update');
        Route::get('/api/conversations', [BoardCommunityController::class, 'conversations'])->name('conversations');
        Route::post('/api/conversations', [BoardCommunityController::class, 'createConversation'])->middleware('throttle:10,1')->name('conversations.create');
        Route::get('/api/conversations/{conversation}/messages', [BoardCommunityController::class, 'messages'])->name('messages');
        Route::post('/api/conversations/{conversation}/messages', [BoardCommunityController::class, 'send'])->middleware('throttle:30,1')->name('messages.send');
        Route::get('/api/admin/pending', [BoardAdminController::class, 'pending'])->name('admin.pending');
        Route::post('/api/admin/invitations', [BoardAdminController::class, 'invite'])->middleware('throttle:10,1')->name('admin.invite');
        Route::patch('/api/admin/members/{member}', [BoardAdminController::class, 'decision'])->name('admin.decision');
        Route::post('/logout', [BoardAccessController::class, 'logout'])->name('logout');
    });
});
