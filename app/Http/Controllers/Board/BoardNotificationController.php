<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $memberId = $request->user('board')->id;
        return response()->json([
            'unread_count' => DB::table('board_notifications')->where('member_id', $memberId)->whereNull('read_at')->count(),
            'items' => DB::table('board_notifications')->where('member_id', $memberId)
                ->select('id', 'category', 'title', 'body', 'url', 'read_at', 'created_at')
                ->orderByDesc('id')->limit(50)->get(),
        ]);
    }

    public function markRead(Request $request, int $notification): JsonResponse
    {
        $updated = DB::table('board_notifications')
            ->where('id', $notification)->where('member_id', $request->user('board')->id)
            ->update(['read_at' => now(), 'updated_at' => now()]);
        abort_unless($updated, 404);
        return response()->json(['saved' => true]);
    }
}
