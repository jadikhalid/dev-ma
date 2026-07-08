<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTalentIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->isTalent() || $user->isCompany()) && ! $user->isApproved()) {
            if ($user->isRejected()) {
                return redirect()->route('account.rejected');
            }

            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
