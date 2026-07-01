<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTalentIsRejected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isTalent() || ! $user->isRejected()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
