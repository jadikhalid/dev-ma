<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use App\Services\CompanyProfileCompletionService;
use App\Services\TalentDashboardStatsService;
use App\Services\TalentProfileCompletionService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private TalentProfileCompletionService $profileCompletion,
        private CompanyProfileCompletionService $companyProfileCompletion,
        private AdminDashboardService $adminDashboard,
        private TalentDashboardStatsService $talentStats,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStaff()) {
            return view('dashboard.admin', [
                'dashboard' => $this->adminDashboard->build($user),
            ]);
        }

        if ($user->isCompany()) {
            $user->load('companyProfile');
            $profile = $user->companyProfile;
            $completion = $this->companyProfileCompletion->assess($profile);
            $recentRequests = $user->recruitmentRequests()->with('talent.profile')->latest()->take(5)->get();

            return view('dashboard.company', compact('recentRequests', 'profile', 'completion'));
        }

        $user->load(['profile.profession', 'profile.professionSector', 'profile.documents']);
        $profile = $user->profile;
        $completion = $this->profileCompletion->assess($profile);
        $stats = $this->talentStats->build($user);

        return view('dashboard.talent', compact('profile', 'completion', 'stats'));
    }
}
