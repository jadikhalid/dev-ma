<?php

namespace App\Http\Middleware;

use App\Models\ModeratorPermissionCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModeratorPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! ModeratorPermissionCatalog::isValid($permission)) {
            abort(403);
        }

        if (! $user->hasModeratorPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
