<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\SocialPost;
use App\Models\User;
use App\Support\BlogCoverStorage;
use Illuminate\Support\Str;

class NewsletterRenderer
{
    /**
     * @param  list<array<string, mixed>>|null  $blocks
     */
    public function renderHtml(Newsletter $newsletter, ?NewsletterSubscriber $subscriber = null, ?array $blocks = null): string
    {
        $blocks = $blocks ?? $newsletter->normalizedBlocks();
        $parts = [];

        foreach ($blocks as $block) {
            $html = $this->renderBlock($block, $newsletter->locale);
            if ($html !== '') {
                $parts[] = $html;
            }
        }

        $unsubscribeUrl = $subscriber
            ? route('newsletter.unsubscribe', ['token' => $subscriber->ensureUnsubscribeToken()])
            : '#';

        $body = implode("\n", $parts);
        if ($body === '') {
            $body = '<p style="margin:0;font-size:15px;line-height:1.6;color:#374151;">'
                .e(__('talenma.newsletter.email_empty_body'))
                .'</p>';
        }

        $footer = '<p style="margin:28px 0 0;font-size:12px;line-height:1.6;color:#9ca3af;">'
            .e(__('talenma.newsletter.email_unsubscribe_prompt'))
            .' <a href="'.e($unsubscribeUrl).'" style="color:#4f46e5;text-decoration:underline;">'
            .e(__('talenma.newsletter.email_unsubscribe'))
            .'</a></p>';

        return view('emails.newsletter-campaign', [
            'bodyHtml' => $body.$footer,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $block
     */
    public function renderBlock(array $block, string $locale): string
    {
        $type = (string) ($block['type'] ?? '');

        return match ($type) {
            Newsletter::BLOCK_HEADER => $this->renderHeader($block),
            Newsletter::BLOCK_HERO => $this->renderHero($block),
            Newsletter::BLOCK_JOBS => $this->renderJobs($block),
            Newsletter::BLOCK_BLOG => $this->renderBlog($block, $locale),
            Newsletter::BLOCK_SOCIAL => $this->renderSocial($block),
            Newsletter::BLOCK_TALENTS => $this->renderTalents($block),
            Newsletter::BLOCK_COMPANIES => $this->renderCompanies($block),
            Newsletter::BLOCK_STATS => $this->renderStats($block),
            Newsletter::BLOCK_TEXT => $this->renderText($block),
            Newsletter::BLOCK_CTA => $this->renderCta($block),
            default => '',
        };
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderHeader(array $block): string
    {
        $title = trim((string) ($block['title'] ?? ''));
        $subtitle = trim((string) ($block['subtitle'] ?? ''));
        if ($title === '' && $subtitle === '') {
            return '';
        }

        $html = '';
        if ($title !== '') {
            $html .= '<h1 style="margin:0 0 8px;font-size:22px;line-height:1.3;color:#111827;">'.e($title).'</h1>';
        }
        if ($subtitle !== '') {
            $html .= '<p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#4b5563;">'.e($subtitle).'</p>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderHero(array $block): string
    {
        $url = trim((string) ($block['image_url'] ?? ''));
        if ($url === '') {
            return '';
        }

        $alt = trim((string) ($block['alt'] ?? ''));

        return '<p style="margin:0 0 20px;"><img src="'.e($url).'" alt="'.e($alt).'" style="display:block;width:100%;max-width:496px;height:auto;border-radius:12px;"></p>';
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderJobs(array $block): string
    {
        $ids = $this->idList($block['job_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $jobs = JobPosting::query()
            ->with(['companyProfile.user', 'professionSector'])
            ->whereIn('id', $ids)
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->get()
            ->sortBy(fn (JobPosting $job) => array_search($job->id, $ids, true));

        if ($jobs->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_jobs_heading')));
        $html = '<h2 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#111827;">'.e($heading).'</h2>';

        foreach ($jobs as $job) {
            $url = route('jobs.gate', $job);
            $html .= '<div style="margin:0 0 12px;padding:12px 14px;border:1px solid #e5e7eb;border-radius:12px;">'
                .'<p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#6b7280;">'.e($job->advertiserName()).'</p>'
                .'<p style="margin:0 0 6px;font-size:15px;font-weight:700;color:#111827;"><a href="'.e($url).'" style="color:#111827;text-decoration:none;">'.e($job->title).'</a></p>';
            if ($job->sectorLabel() !== '') {
                $html .= '<p style="margin:0 0 8px;font-size:13px;color:#6b7280;">'.e($job->sectorLabel()).'</p>';
            }
            $html .= '<a href="'.e($url).'" style="font-size:13px;font-weight:600;color:#4f46e5;text-decoration:none;">'
                .e(__('talenma.newsletter.view_job'))
                .'</a></div>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderBlog(array $block, string $locale): string
    {
        $ids = $this->idList($block['post_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $posts = BlogPost::query()
            ->whereIn('id', $ids)
            ->published()
            ->get()
            ->sortBy(fn (BlogPost $post) => array_search($post->id, $ids, true));

        if ($posts->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_blog_heading')));
        $html = '<h2 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#111827;">'.e($heading).'</h2>';

        foreach ($posts as $post) {
            $url = route('blog.show', $post->slug);
            $cover = BlogCoverStorage::url($post->cover_path);
            $html .= '<div style="margin:0 0 14px;">';
            if ($cover) {
                $html .= '<a href="'.e($url).'"><img src="'.e($cover).'" alt="" style="display:block;width:100%;max-width:496px;height:auto;border-radius:10px;margin:0 0 8px;"></a>';
            }
            $html .= '<p style="margin:0 0 4px;font-size:15px;font-weight:700;"><a href="'.e($url).'" style="color:#111827;text-decoration:none;">'.e($post->title).'</a></p>'
                .'<p style="margin:0 0 6px;font-size:13px;line-height:1.5;color:#6b7280;">'.e(Str::limit($post->excerpt, 140)).'</p>'
                .'<a href="'.e($url).'" style="font-size:13px;font-weight:600;color:#4f46e5;text-decoration:none;">'
                .e(__('talenma.newsletter.read_article'))
                .'</a></div>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderSocial(array $block): string
    {
        $ids = $this->idList($block['social_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $posts = SocialPost::query()->whereIn('id', $ids)->get()
            ->sortBy(fn (SocialPost $post) => array_search($post->id, $ids, true));

        if ($posts->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_social_heading')));
        $html = '<h2 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#111827;">'.e($heading).'</h2>';

        foreach ($posts as $post) {
            $html .= '<div style="margin:0 0 10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;">'
                .'<p style="margin:0 0 4px;font-size:14px;font-weight:600;color:#111827;">'.e($post->title).'</p>';
            if (filled($post->subtitle)) {
                $html .= '<p style="margin:0 0 6px;font-size:13px;color:#6b7280;">'.e($post->subtitle).'</p>';
            }
            if (filled($post->url)) {
                $html .= '<a href="'.e($post->url).'" style="font-size:13px;font-weight:600;color:#4f46e5;text-decoration:none;">'
                    .e(__('talenma.newsletter.view_external'))
                    .'</a>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderTalents(array $block): string
    {
        $ids = $this->idList($block['user_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $talents = User::query()
            ->with('profile.profession')
            ->whereIn('id', $ids)
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->get()
            ->sortBy(fn (User $user) => array_search($user->id, $ids, true));

        if ($talents->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_talents_heading')));
        $html = '<h2 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#111827;">'.e($heading).'</h2>';

        foreach ($talents as $talent) {
            $meta = array_filter([
                $talent->profile?->profession?->localizedName(),
                $talent->profile?->city,
            ]);
            $html .= '<div style="margin:0 0 10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;">'
                .'<p style="margin:0;font-size:14px;font-weight:700;color:#111827;">'.e($talent->formalDisplayName()).'</p>';
            if ($meta !== []) {
                $html .= '<p style="margin:4px 0 0;font-size:13px;color:#6b7280;">'.e(implode(' · ', $meta)).'</p>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderCompanies(array $block): string
    {
        $ids = $this->idList($block['company_profile_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $companies = CompanyProfile::query()
            ->with(['user', 'professionSector'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (CompanyProfile $profile) => array_search($profile->id, $ids, true));

        if ($companies->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_companies_heading')));
        $html = '<h2 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#111827;">'.e($heading).'</h2>';

        foreach ($companies as $company) {
            $company->loadMissing('professionSector');
            $meta = array_filter([
                $company->professionSector?->localizedName()
                    ?? (filled($company->sector ?? null) ? (string) $company->sector : null),
                filled($company->city) ? (string) $company->city : null,
                filled($company->country) ? CompanyProfile::countryLabelFor($company->country) : null,
            ]);

            $html .= '<div style="margin:0 0 10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;">'
                .'<p style="margin:0;font-size:14px;font-weight:700;color:#111827;">'.e($company->displayName()).'</p>';
            if ($meta !== []) {
                $html .= '<p style="margin:4px 0 0;font-size:13px;color:#6b7280;">'.e(implode(' · ', $meta)).'</p>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderStats(array $block): string
    {
        $items = $block['items'] ?? [];
        if (! is_array($items) || $items === []) {
            return '';
        }

        $cells = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' && $label === '') {
                continue;
            }
            $cells[] = '<td style="padding:10px 8px;text-align:center;vertical-align:top;">'
                .'<div style="font-size:20px;font-weight:700;color:#4f46e5;">'.e($value).'</div>'
                .'<div style="font-size:12px;color:#6b7280;">'.e($label).'</div>'
                .'</td>';
        }

        if ($cells === []) {
            return '';
        }

        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:16px 0;background:#f5f3ff;border-radius:12px;">'
            .'<tr>'.implode('', $cells).'</tr></table>';
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderText(array $block): string
    {
        $body = trim((string) ($block['body'] ?? ''));
        if ($body === '') {
            return '';
        }

        $safe = e($body);
        $safe = nl2br($safe);

        return '<div style="margin:16px 0;font-size:15px;line-height:1.65;color:#374151;">'.$safe.'</div>';
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderCta(array $block): string
    {
        $label = trim((string) ($block['label'] ?? ''));
        $url = trim((string) ($block['url'] ?? ''));
        if ($label === '' || $url === '') {
            return '';
        }

        return '<p style="margin:20px 0;text-align:center;">'
            .'<a href="'.e($url).'" style="display:inline-block;padding:12px 22px;background:#4f46e5;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;border-radius:10px;">'
            .e($label)
            .'</a></p>';
    }

    /**
     * @param  mixed  $raw
     * @return list<int>
     */
    private function idList(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($id) => is_numeric($id) ? (int) $id : null,
            $raw
        ))));
    }
}
