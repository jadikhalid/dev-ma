<?php

namespace App\Http\Controllers;

use App\Models\SocialPost;
use App\Models\User;
use App\Services\CompanyCatalogSearchService;
use App\Services\HeroTalentMosaicService;
use App\Services\ProfessionCatalogService;

class HomeController extends Controller
{
    public function __construct(
        private ProfessionCatalogService $professionCatalog,
        private CompanyCatalogSearchService $companyCatalogSearch,
        private HeroTalentMosaicService $heroTalentMosaic,
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
                ->count(),
            'heroTiles' => $this->heroTalentMosaic->tiles(),
            'socialPosts' => SocialPost::forHomeSlider(),
            'professionSectors' => $this->professionCatalog->sectorsForLocale(),
            'canViewProfiles' => $canViewProfiles,
            'showCompanySearch' => $showCompanySearch,
            'companyCountries' => $showCompanySearch
                ? $this->companyCatalogSearch->availableCountries()
                : [],
            'featuredCompanies' => $this->companyCatalogSearch->featuredForHome(),
        ]);
    }
}
