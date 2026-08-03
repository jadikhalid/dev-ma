<?php

namespace App\Http\Controllers;

use App\Models\DirectHireRequest;
use App\Models\RecruitmentRequest;
use App\Services\AdminDashboardService;
use App\Services\CompanyDashboardActivityService;
use App\Services\CompanyProfileCompletionService;
use App\Services\DashboardActivityToastService;
use App\Services\DirectHireService;
use App\Services\JobPostingService;
use App\Services\RecruitmentRequestService;
use App\Services\StaffRecruitmentActivityService;
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
        private StaffRecruitmentActivityService $staffRecruitmentActivity,
        private RecruitmentRequestService $recruitmentRequests,
        private DashboardActivityToastService $activityToasts,
        private DirectHireService $directHires,
        private JobPostingService $jobs,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStaff()) {
            $recentActivity = $this->staffRecruitmentActivity->recentActivity($user);
            $this->activityToasts->flashUnseen($user, $recentActivity, 'staff');

            $sourcingRequests = RecruitmentRequest::query()
                ->with(['company', 'talent'])
                ->whereIn('status', RecruitmentRequest::openStatuses())
                ->latest()
                ->take(20)
                ->get();

            return view('dashboard.admin', [
                'dashboard' => $this->adminDashboard->build($user),
                'sourcingRequests' => $sourcingRequests,
                'recentActivity' => $recentActivity,
                'sourcingUnseen' => $this->recruitmentRequests->staffHasUnseenChanges($user),
            ]);
        }

        if ($user->isCompany()) {
            $profile = $user->companyOrganization();
            $completion = $this->companyProfileCompletion->assess($profile);
            $recentRequests = $user->isCompanyOwner()
                ? $user->recruitmentRequests()
                    ->with('talent.profile')
                    ->whereIn('status', RecruitmentRequest::openStatuses())
                    ->latest()
                    ->take(5)
                    ->get()
                : collect();

            $directHires = $this->directHires->queryForCompany($user)
                ->with(['talent.profile', 'rounds'])
                ->whereIn('status', DirectHireRequest::openStatuses())
                ->latest()
                ->take(5)
                ->get();
            $directHireUnseen = $this->directHires->companyHasUnseenChanges($user);
            $recentActivity = $this->companyActivity->recentActivity($user);
            $this->activityToasts->flashUnseen($user, $recentActivity, 'company');

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
        $this->activityToasts->flashUnseen($user, $stats['recent_activity'] ?? [], 'talent');
        $openDirectHires = DirectHireRequest::query()
            ->where('talent_user_id', $user->id)
            ->whereIn('status', DirectHireRequest::openStatuses())
            ->count();
        $jobsUnseen = $this->jobs->talentHasUnseenChanges($user);

        return view('dashboard.talent', compact('profile', 'completion', 'stats', 'openDirectHires', 'jobsUnseen'));
    }
}
