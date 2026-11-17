<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardOpportunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->validate(['type' => ['sometimes', Rule::in(['ask', 'offer'])]])['type'] ?? null;
        $query = DB::table('board_opportunities as o')
            ->join('board_members as m', 'm.id', '=', 'o.member_id')
            ->where('o.status', 'open')->where('m.status', 'active')
            ->select('o.id', 'o.member_id', 'o.type', 'o.title', 'o.details', 'o.created_at', 'm.name as member_name', 'm.company');
        if ($type) {
            $query->where('o.type', $type);
        }
        return response()->json($query->orderByDesc('o.id')->paginate(30));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['ask', 'offer'])],
            'title' => ['required', 'string', 'max:180'],
            'details' => ['required', 'string', 'max:3000'],
        ]);
        $id = DB::table('board_opportunities')->insertGetId([
            ...$data, 'member_id' => $request->user('board')->id, 'status' => 'open',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['id' => $id], 201);
    }

    public function close(Request $request, int $opportunity): JsonResponse
    {
        $updated = DB::table('board_opportunities')
            ->where('id', $opportunity)->where('member_id', $request->user('board')->id)
            ->where('status', 'open')->update(['status' => 'closed', 'updated_at' => now()]);
        abort_unless($updated, 404);
        return response()->json(['saved' => true]);
    }
}
