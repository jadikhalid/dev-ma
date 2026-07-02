<?php

namespace App\Http\Controllers;

use App\Models\SocialPost;
use App\Models\User;
use App\Services\ProfessionCatalogService;

class HomeController extends Controller
{
    public function __construct(private ProfessionCatalogService $professionCatalog) {}

    public function index()
    {
        return view('home', [
            'talentsCount' => User::where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->where('is_subscribed', true)
                ->count(),
            'socialPosts' => SocialPost::forSlider(),
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
        ]);
    }
}
