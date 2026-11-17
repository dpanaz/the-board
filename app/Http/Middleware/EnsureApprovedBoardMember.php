<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedBoardMember
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user('board')?->isApproved(), 403, 'Board membership requires administrator approval.');

        return $next($request);
    }
}
