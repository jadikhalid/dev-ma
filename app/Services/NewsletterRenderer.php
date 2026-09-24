<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\LibraryBook;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\SocialPost;
use App\Models\User;
use App\Support\BlogCoverStorage;
use App\Support\TalentCv\TalentCvMarketingPreview;
use App\Support\TalentCv\TalentCvTemplateCatalog;
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

        $body = implode("\n", $parts);
        if ($body === '') {
            $body = '<p style="margin:0;font-size:15px;line-height:1.6;color:#374151;">'
                .e(__('talenma.newsletter.email_empty_body'))
                .'</p>';
        }

        return view('emails.newsletter-campaign', [
            'bodyHtml' => $body,
            'datedTitle' => $this->datedTitle($newsletter),
        ])->render();
    }

    private function datedTitle(Newsletter $newsletter): string
    {
        $locale = $newsletter->locale === Newsletter::LOCALE_EN ? 'en' : 'fr';
        $date = $newsletter->sent_at
            ?? $newsletter->scheduled_at
            ?? now();

        $formatted = $date->copy()->locale($locale)->translatedFormat('l j F Y');
        $parts = preg_split('/\s+/u', $formatted) ?: [];
        $titled = implode(' ', array_map(function (string $part): string {
            if (preg_match('/^\d+$/u', $part)) {
                return $part;
            }

            return mb_strtoupper(mb_substr($part, 0, 1)).mb_substr($part, 1);
        }, $parts));

        return __('talenma.newsletter.email_dated_title', ['date' => $titled], $locale);
    }

    private function sectionHeading(string $heading): string
    {
        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0 16px;">'
            .'<tr><td style="padding:10px 14px;background:#eef2ff;font-size:15px;line-height:1.35;font-weight:700;color:#3730a3;">'
            .e($heading)
            .'</td></tr>'
            .'</table>';
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
            Newsletter::BLOCK_REGISTER => $this->renderRegister(),
            Newsletter::BLOCK_LIBRARY => $this->renderLibrary($block, $locale),
            Newsletter::BLOCK_CV_TEMPLATES => $this->renderCvTemplates($block, $locale),
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

        return '<p style="margin:0 0 20px;"><img src="'.e($url).'" alt="'.e($alt).'" style="display:block;width:100%;max-width:664px;height:auto;border-radius:12px;"></p>';
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
        $html = $this->sectionHeading($heading !== '' ? $heading : __('talenma.newsletter.block_jobs_heading'));

        foreach ($jobs as $job) {
            $html .= $this->jobCard($job);
        }

        return $html;
    }

    private function jobCard(JobPosting $job): string
    {
        $url = route('jobs.public.show', $job);
        $logo = $this->absolutePublicUrl($job->advertiserLogoUrl());
        $initials = e($job->advertiserInitials());

        $thumb = $logo
            ? '<img src="'.e($logo).'" alt="" width="56" height="56" style="display:block;float:right;width:56px;height:56px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">'
            : '<div style="float:right;width:56px;height:56px;border-radius:10px;background:#4f46e5;color:#ffffff;font-size:14px;font-weight:800;line-height:56px;text-align:center;">'.$initials.'</div>';

        $html = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 12px;border:1px solid #e5e7eb;border-radius:12px;">'
            .'<tr>'
            .'<td valign="top" style="padding:0;">'
            .'<a href="'.e($url).'" style="display:block;padding:12px 14px;text-decoration:none;color:inherit;">'
            .$thumb
            .'<p style="margin:0 64px 4px 0;font-size:12px;font-weight:600;color:#6b7280;">'.e($job->advertiserName()).'</p>'
            .'<p style="margin:0 64px 6px 0;font-size:15px;font-weight:700;color:#111827;">'.e($job->title).'</p>';

        if ($job->sectorLabel() !== '') {
            $html .= '<p style="margin:0 64px 8px 0;font-size:13px;color:#6b7280;">'.e($job->sectorLabel()).'</p>';
        }

        $html .= '<span style="font-size:13px;font-weight:600;color:#4f46e5;">'
            .e(__('talenma.newsletter.view_job'))
            .'</span>'
            .'<div style="clear:both;line-height:0;height:0;"></div>'
            .'</a>'
            .'</td>'
            .'</tr></table>';

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
        $html = $this->sectionHeading($heading !== '' ? $heading : __('talenma.newsletter.block_blog_heading'));

        foreach ($posts as $post) {
            $url = route('blog.show', $post->slug);
            $cover = BlogCoverStorage::url($post->cover_path);
            $html .= '<div style="margin:0 0 14px;">';
            if ($cover) {
                $html .= '<a href="'.e($url).'"><img src="'.e($cover).'" alt="" style="display:block;width:100%;max-width:664px;height:auto;border-radius:10px;margin:0 0 8px;"></a>';
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
            ->with(['profile.profession', 'profile.professionSector'])
            ->whereIn('id', $ids)
            ->where('role', 'dev')
            ->where('approval_status', User::APPROVAL_APPROVED)
            ->get()
            ->sortBy(fn (User $user) => array_search($user->id, $ids, true));

        if ($talents->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_talents_heading')));
        $html = $this->sectionHeading($heading !== '' ? $heading : __('talenma.newsletter.block_talents_heading'));
        $html .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 8px;"><tr>';

        $index = 0;
        foreach ($talents as $talent) {
            if ($index > 0 && $index % 2 === 0) {
                $html .= '</tr><tr>';
            }

            $html .= '<td width="50%" valign="top" style="padding:0 6px 12px;">'.$this->talentCard($talent).'</td>';
            $index++;
        }

        if ($index % 2 === 1) {
            $html .= '<td width="50%" valign="top" style="padding:0 6px 12px;"></td>';
        }

        $html .= '</tr></table>';

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderLibrary(array $block, string $locale): string
    {
        $ids = $this->idList($block['book_ids'] ?? []);
        if ($ids === []) {
            return '';
        }

        $latestIds = LibraryBook::query()
            ->published()
            ->latest('id')
            ->limit(Newsletter::LIBRARY_LATEST_LIMIT)
            ->pluck('id')
            ->all();

        $ids = array_values(array_filter(
            $ids,
            fn (int $id) => in_array($id, $latestIds, true)
        ));
        $ids = array_slice($ids, 0, Newsletter::LIBRARY_LATEST_LIMIT);

        if ($ids === []) {
            return '';
        }

        $books = LibraryBook::query()
            ->with('category.parent.parent')
            ->published()
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (LibraryBook $book) => array_search($book->id, $ids, true));

        if ($books->isEmpty()) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_library_heading')));
        $html = $this->sectionHeading($heading !== '' ? $heading : __('talenma.newsletter.block_library_heading'));
        $html .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 8px;"><tr>';

        $index = 0;
        foreach ($books as $book) {
            if ($index > 0 && $index % 3 === 0) {
                $html .= '</tr><tr>';
            }

            $html .= '<td width="33%" valign="top" style="padding:0 4px 10px;width:33%;height:176px;">'.$this->libraryCard($book, $locale).'</td>';
            $index++;
        }

        $remainder = $index % 3;
        if ($remainder !== 0) {
            for ($pad = $remainder; $pad < 3; $pad++) {
                $html .= '<td width="33%" valign="top" style="padding:0 4px 10px;width:33%;height:176px;"></td>';
            }
        }

        $html .= '</tr></table>';

        return $html;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function renderCvTemplates(array $block, string $locale): string
    {
        $keys = $block['template_keys'] ?? [];
        if (! is_array($keys) || $keys === []) {
            return '';
        }

        $validKeys = [];
        foreach ($keys as $key) {
            $key = is_string($key) ? $key : '';
            if (TalentCvTemplateCatalog::isValidTemplate($key) && ! in_array($key, $validKeys, true)) {
                $validKeys[] = $key;
            }
        }

        if ($validKeys === []) {
            return '';
        }

        $heading = trim((string) ($block['heading'] ?? __('talenma.newsletter.block_cv_templates_heading')));
        $html = $this->sectionHeading($heading !== '' ? $heading : __('talenma.newsletter.block_cv_templates_heading'));

        $descriptions = is_array($block['template_descriptions'] ?? null)
            ? $block['template_descriptions']
            : [];

        foreach ($validKeys as $key) {
            $description = trim((string) ($descriptions[$key] ?? ''));
            $html .= $this->cvTemplateRow($key, $locale, $description);
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

        return '<div style="margin:16px 0;padding:14px 16px;border:1px solid #e5e7eb;border-radius:12px;background:#f9fafb;">'
            .'<p style="margin:0;font-size:15px;line-height:1.65;color:#374151;">'.$safe.'</p>'
            .'</div>';
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

    private function renderRegister(): string
    {
        $url = 'https://talentsdumaroc.com/register';
        $title = __('talenma.newsletter.register_banner_title');
        $body = __('talenma.newsletter.register_banner_body');
        $cta = __('talenma.newsletter.register_banner_cta');

        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;">'
            .'<tr><td style="padding:22px 20px;background:#4f46e5;text-align:center;">'
            .'<p style="margin:0 0 10px;font-size:22px;line-height:1.25;font-weight:800;color:#ffffff;">'.e($title).'</p>'
            .'<p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#e0e7ff;">'.e($body).'</p>'
            .'<a href="'.e($url).'" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:12px 22px;background:#fbbf24;color:#1f2937;font-size:14px;font-weight:800;text-decoration:none;">'
            .e($cta)
            .'</a>'
            .'</td></tr></table>';
    }

    private function talentCard(User $talent): string
    {
        $name = $talent->formalDisplayName();
        $sector = $talent->profile?->professionSector?->localizedName() ?? '';
        $profession = $talent->profile?->profession?->localizedName() ?? '';
        $city = trim((string) ($talent->profile?->city ?? ''));
        $photo = $this->absolutePublicUrl($talent->avatarUrl());
        $initials = e($talent->initials());

        $photoHtml = $photo
            ? '<img src="'.e($photo).'" alt="" width="56" height="56" style="display:block;width:56px;height:56px;object-fit:cover;border-radius:50%;border:2px solid #e0e7ff;">'
            : '<div style="width:56px;height:56px;border-radius:50%;background:#e0e7ff;color:#3730a3;font-size:16px;font-weight:800;line-height:56px;text-align:center;">'.$initials.'</div>';

        $html = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;background:#ffffff;">'
            .'<tr><td style="padding:12px 12px 10px;text-align:center;">'
            .'<div style="margin:0 auto 8px;width:56px;">'.$photoHtml.'</div>'
            .'<p style="margin:0 0 4px;font-size:13px;line-height:1.3;font-weight:800;color:#111827;">'.e($name).'</p>';

        if ($sector !== '') {
            $html .= '<p style="margin:0 0 2px;font-size:11px;line-height:1.35;color:#4f46e5;font-weight:700;">'.e($sector).'</p>';
        }
        if ($profession !== '') {
            $html .= '<p style="margin:0 0 2px;font-size:12px;line-height:1.35;color:#6b7280;">'.e($profession).'</p>';
        }
        if ($city !== '') {
            $html .= '<p style="margin:0;font-size:11px;line-height:1.35;color:#6b7280;">'.e($city).'</p>';
        }

        $html .= '</td></tr></table>';

        return $html;
    }

    private function libraryCard(LibraryBook $book, string $locale): string
    {
        $title = trim($book->title);
        if (mb_strlen($title) > 56) {
            $title = rtrim(mb_substr($title, 0, 55)).'…';
        }
        $discipline = $book->category?->rootAncestor()->localizedName($locale) ?? '';
        $cover = $this->absolutePublicUrl($book->coverUrl());
        $url = route('library.gate');

        $coverHtml = $cover
            ? '<img src="'.e($cover).'" alt="" width="96" height="88" style="display:block;width:96px;height:88px;max-width:96px;object-fit:cover;margin:8px auto 0;border:0;">'
            : '<div style="width:96px;height:88px;margin:8px auto 0;background:#fef3c7;"></div>';

        $html = '<a href="'.e($url).'" style="text-decoration:none;color:inherit;">'
            .'<table role="presentation" width="100%" height="176" cellspacing="0" cellpadding="0" style="height:176px;width:100%;border:1px solid #e5e7eb;background:#ffffff;">'
            .'<tr><td valign="top" height="176" style="padding:0 4px 8px;height:176px;text-align:center;">'
            .$coverHtml
            .'<p style="margin:8px 2px 3px;height:28px;max-height:28px;line-height:14px;font-size:11px;font-weight:800;color:#111827;overflow:hidden;">'.e($title).'</p>';

        if ($discipline !== '') {
            $html .= '<p style="margin:0 2px 2px;height:14px;max-height:14px;line-height:14px;font-size:10px;color:#4f46e5;font-weight:700;overflow:hidden;white-space:nowrap;">'.e($discipline).'</p>';
        }

        $html .= '</td></tr></table></a>';

        return $html;
    }

    private function cvTemplateRow(string $key, string $locale, string $description): string
    {
        $label = __('talenma.cv_builder.templates.'.$key, [], $locale === 'en' ? 'en' : 'fr');
        $preview = $this->absolutePublicUrl(TalentCvMarketingPreview::imagePath($key, $locale));
        $url = route('cv-builder.gate');

        $previewHtml = $preview
            ? '<img src="'.e($preview).'" alt="" width="96" style="display:block;width:96px;max-width:100%;height:88px;object-fit:cover;object-position:top;border:0;">'
            : '<div style="width:96px;height:88px;background:#eef2ff;"></div>';

        $right = '<p style="margin:0 0 4px;font-size:13px;line-height:1.3;font-weight:800;color:#111827;">'.e($label).'</p>';
        if ($description !== '') {
            $right .= '<p style="margin:0;font-size:12px;line-height:1.5;color:#4b5563;">'.nl2br(e($description), false).'</p>';
        }

        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 10px;border:1px solid #e5e7eb;background:#ffffff;">'
            .'<tr>'
            .'<td width="96" valign="top" style="padding:8px;width:96px;">'
            .'<a href="'.e($url).'" style="text-decoration:none;">'.$previewHtml.'</a>'
            .'</td>'
            .'<td valign="middle" style="padding:8px 12px 8px 4px;">'
            .'<a href="'.e($url).'" style="text-decoration:none;color:inherit;">'.$right.'</a>'
            .'</td>'
            .'</tr></table>';
    }

    private function absolutePublicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
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
