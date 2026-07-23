<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Services\LocaleFromIpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function suggest(Request $request, LocaleFromIpService $localeFromIp): JsonResponse
    {
        return response()->json([
            'locale' => $localeFromIp->suggestLocale($request),
            'current' => app()->getLocale(),
        ]);
    }

    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            abort(404);
        }

        session([
            'locale' => $locale,
        ]);

        if ($request->boolean('manual', true)) {
            session(['locale_manual' => true]);
        }

        return redirect()->to(
            $request->headers->get('Referer') ?: url()->previous() ?: route('home')
        );
    }
}
