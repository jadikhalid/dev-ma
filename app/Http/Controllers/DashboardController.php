<?php

namespace App\Http\Controllers;

use App\Models\DirectHireRequest;
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
            $profile = $user->companyOrganization();
            $completion = $this->companyProfileCompletion->assess($profile);
            $recentRequests = $user->isCompanyOwner()
                ? $user->recruitmentRequests()->with('talent.profile')->latest()->take(5)->get()
                : collect();

            $directHireQuery = DirectHireRequest::query()
                ->with(['talent.profile', 'rounds'])
                ->latest()
                ->take(8);

            if ($profile) {
                $directHireQuery->where('company_profile_id', $profile->id);
            } else {
                $directHireQuery->where('company_user_id', $user->id);
            }

            $directHires = $directHireQuery->get();

            return view('dashboard.company', compact('recentRequests', 'directHires', 'profile', 'completion'));
        }

        $user->load(['profile.profession', 'profile.professionSector', 'profile.documents']);
        $profile = $user->profile;
        $completion = $this->profileCompletion->assess($profile);
        $stats = $this->talentStats->build($user);
        $directHires = DirectHireRequest::query()
            ->where('talent_user_id', $user->id)
            ->with(['companyProfile', 'company'])
            ->latest()
            ->take(8)
            ->get();
        $pendingDirectHires = $directHires->whereIn('status', [
            DirectHireRequest::STATUS_PENDING_RESPONSE,
            DirectHireRequest::STATUS_DEFERRED,
        ])->count();

        return view('dashboard.talent', compact('profile', 'completion', 'stats', 'directHires', 'pendingDirectHires'));
    }
}
