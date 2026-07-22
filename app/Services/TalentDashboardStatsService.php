<?php

namespace App\Services;

use App\Models\ProfileDocumentDownload;
use App\Models\ProfileView;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Carbon\CarbonInterface;

class TalentDashboardStatsService
{
    public function __construct(
        private MessagingService $messaging,
    ) {}

    /**
     * @return array{
     *     profile_views_7d: int,
     *     profile_views_total: int,
     *     cv_downloads_7d: int,
     *     unread_messages: int,
     *     recruitment_requests_total: int,
     *     recent_activity: list<array{type: string, actor: string, detail: ?string, at: CarbonInterface}>
     * }
     */
    public function build(User $talent): array
    {
        $since = now()->subDays(7);

        $views7d = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        $viewsTotal = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->count();

        $downloads7d = ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->where('created_at', '>=', $since)
            ->count();

        $recruitmentTotal = RecruitmentRequest::query()
            ->where('developer_user_id', $talent->id)
            ->count();

        return [
            'profile_views_7d' => $views7d,
            'profile_views_total' => $viewsTotal,
            'cv_downloads_7d' => $downloads7d,
            'unread_messages' => $this->messaging->unreadCountFor($talent),
            'recruitment_requests_total' => $recruitmentTotal,
            'recent_activity' => $this->recentActivity($talent),
        ];
    }

    /**
     * @return list<array{type: string, actor: string, detail: ?string, at: CarbonInterface}>
     */
    private function recentActivity(User $talent, int $limit = 8): array
    {
        $views = ProfileView::query()
            ->where('talent_user_id', $talent->id)
            ->with('viewer.companyProfile')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileView $view) => [
                'type' => 'profile_view',
                'actor' => $this->actorName($view->viewer),
                'detail' => null,
                'at' => $view->created_at,
            ]);

        $downloads = ProfileDocumentDownload::query()
            ->where('talent_user_id', $talent->id)
            ->with(['downloader.companyProfile', 'document'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (ProfileDocumentDownload $download) => [
                'type' => 'cv_download',
                'actor' => $this->actorName($download->downloader),
                'detail' => $download->document?->languageLabel(),
                'at' => $download->created_at,
            ]);

        return $views
            ->concat($downloads)
            ->sortByDesc(fn (array $item) => $item['at']?->timestamp ?? 0)
            ->take($limit)
            ->values()
            ->all();
    }

    private function actorName(?User $user): string
    {
        if (! $user) {
            return __('talenma.dashboard.talent.stats.unknown_actor');
        }

        $user->loadMissing('companyProfile');

        return $user->name
            ?: __('talenma.dashboard.talent.stats.unknown_actor');
    }
}
