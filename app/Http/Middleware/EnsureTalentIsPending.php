<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTalentIsPending
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isTalent() && ! $user->isCompany())) {
            return redirect()->route('dashboard');
        }

        if ($user->isApproved()) {
            return redirect()->route('dashboard');
        }

        if ($user->isRejected()) {
            return redirect()->route('account.rejected');
        }

        return $next($request);
    }
}
