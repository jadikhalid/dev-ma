<?php

namespace App\Http\Controllers;

use App\Models\DirectHireRequest;
use App\Services\AdminDashboardService;
use App\Services\CompanyDashboardActivityService;
use App\Services\CompanyProfileCompletionService;
use App\Services\DirectHireService;
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
        private CompanyDashboardActivityService $companyActivity,
        private DirectHireService $directHires,
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
            $profile = $user->companyOrganization();
            $completion = $this->companyProfileCompletion->assess($profile);
            $recentRequests = $user->isCompanyOwner()
                ? $user->recruitmentRequests()->with('talent.profile')->latest()->take(5)->get()
                : collect();

            $directHires = $this->directHires->queryForCompany($user)
                ->with(['talent.profile', 'rounds'])
                ->whereIn('status', DirectHireRequest::openStatuses())
                ->latest()
                ->take(5)
                ->get();
            $directHireUnseen = $this->directHires->companyHasUnseenChanges($user);
            $recentActivity = $this->companyActivity->recentActivity($user);

            return view('dashboard.company', compact(
                'recentRequests',
                'directHires',
                'profile',
                'completion',
                'directHireUnseen',
                'recentActivity',
            ));
        }

        $user->load(['profile.profession', 'profile.professionSector', 'profile.documents']);
        $profile = $user->profile;
        $completion = $this->profileCompletion->assess($profile);
        $stats = $this->talentStats->build($user);
        $openDirectHires = DirectHireRequest::query()
            ->where('talent_user_id', $user->id)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->count();

        return view('dashboard.talent', compact('profile', 'completion', 'stats', 'openDirectHires'));
    }
}
