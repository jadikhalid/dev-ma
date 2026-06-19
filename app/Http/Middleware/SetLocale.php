<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['fr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        // Première visite (sans choix explicite via le sélecteur) : français par défaut.
        $locale = session('locale', 'fr');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = 'fr';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
