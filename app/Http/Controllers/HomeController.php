<?php

namespace App\Http\Controllers;

use App\Models\ProfessionSector;
use App\Models\SocialPost;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        $professionSectors = ProfessionSector::query()
            ->where('is_active', true)
            ->with(['professions' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->with(['suggestions' => fn ($suggestions) => $suggestions
                    ->where('is_active', true)
                    ->orderBy('sort_order')])])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProfessionSector $sector) => [
                'slug' => $sector->slug,
                'name' => $sector->localizedName($locale),
                'professions' => $sector->professions->map(fn ($profession) => [
                    'slug' => $profession->slug,
                    'name' => $profession->localizedName($locale),
                    'specializations' => $profession->suggestions
                        ->map(fn ($suggestion) => $suggestion->localizedLabel($locale))
                        ->values(),
                ])->values(),
            ])
            ->values();

        return view('home', [
            'talentsCount' => User::where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->where('is_subscribed', true)
                ->count(),
            'socialPosts' => SocialPost::forSlider(),
            'professionSectors' => $professionSectors,
        ]);
    }
}
