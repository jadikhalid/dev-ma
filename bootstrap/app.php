<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsureUserIsNotDisabled::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'staff' => \App\Http\Middleware\EnsureUserIsStaff::class,
            'talent.approved' => \App\Http\Middleware\EnsureTalentIsApproved::class,
            'talent.pending' => \App\Http\Middleware\EnsureTalentIsPending::class,
            'talent.rejected' => \App\Http\Middleware\EnsureTalentIsRejected::class,
            'account.approved' => \App\Http\Middleware\EnsureTalentIsApproved::class,
            'account.pending' => \App\Http\Middleware\EnsureTalentIsPending::class,
            'account.rejected' => \App\Http\Middleware\EnsureTalentIsRejected::class,
            'company.owner' => \App\Http\Middleware\EnsureCompanyOwner::class,
            'company.jobs' => \App\Http\Middleware\EnsureCompanyCanAccessJobs::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
