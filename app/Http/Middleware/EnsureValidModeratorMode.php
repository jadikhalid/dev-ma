<?php

namespace App\Http\Middleware;

use App\Services\ModeratorAssignmentService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidModeratorMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && session()->get(ModeratorAssignmentService::SESSION_MODE_KEY) && ! $user->canActAsModerator()) {
            session()->forget(ModeratorAssignmentService::SESSION_MODE_KEY);
        }

        return $next($request);
    }
}
