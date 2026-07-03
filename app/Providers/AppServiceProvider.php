<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)
            ->max(128)
            ->letters()
            ->numbers());

        if ($this->app->environment('local') && config('mail.default') === 'log') {
            logger()->warning('MAIL_MAILER=log : les e-mails sont écrits dans storage/logs/laravel.log, pas dans Mailpit. Utilisez MAIL_MAILER=smtp et MAIL_PORT=1025.');
        }
    }
}
