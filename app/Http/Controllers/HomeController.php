<?php

namespace App\Http\Controllers;

use App\Models\SocialPost;
use App\Models\User;
use App\Services\CompanyCatalogSearchService;
use App\Services\ProfessionCatalogService;

class HomeController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private CompanyCatalogSearchService $companyCatalogSearch,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $canViewProfiles = $user
            && $user->isCompany()
            && $user->isApproved();

        $showCompanySearch = $user
            && $user->isTalent()
            && $user->isApproved();

        return view('home', [
            'talentsCount' => User::where('role', 'dev')
                ->where('approval_status', User::APPROVAL_APPROVED)
                ->where('is_subscribed', true)
                ->count(),
            'socialPosts' => SocialPost::forSlider(),
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'canViewProfiles' => $canViewProfiles,
            'showCompanySearch' => $showCompanySearch,
            'companyCountries' => $showCompanySearch
                ? $this->companyCatalogSearch->availableCountries()
                : [],
        ]);
    }
}
