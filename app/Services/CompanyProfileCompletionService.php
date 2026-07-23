<?php

namespace App\Services;

use App\Models\CompanyProfile;

class CompanyProfileCompletionService
{
    /**
     * Progress for the company dashboard (informational only — does not gate feature access).
     *
     * @return array{
     *     percent: int,
     *     status: string,
     *     sections: array<string, array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool}>}>,
     *     next_section: string|null,
     *     is_catalog_ready: bool
     * }
     */
    public function assess(?CompanyProfile $profile): array
    {
        if (! $profile) {
            return $this->emptyAssessment();
        }

        $profile->loadMissing('user');

        $sections = [
            'identity' => $this->identitySection($profile),
            'presentation' => $this->presentationSection($profile),
            'hiring' => $this->hiringSection($profile),
            'contact' => $this->contactSection($profile),
        ];

        $requiredPercent = (int) round(collect($sections)
            ->only(['identity', 'presentation', 'hiring'])
            ->avg(fn (array $section) => $section['percent']));

        $percent = min(100, (int) round(($requiredPercent * 0.85) + ($sections['contact']['percent'] * 0.15)));

        $nextSection = null;

        foreach (['identity', 'presentation', 'hiring', 'contact'] as $key) {
            if (! $sections[$key]['complete']) {
                $nextSection = $key;
                break;
            }
        }

        $status = match (true) {
            $percent >= 100 => 'complete',
            $percent >= 40 => 'in_progress',
            default => 'starter',
        };

        $isCatalogReady = filled($profile->user?->name)
            && filled($profile->sector)
            && filled($profile->employee_count)
            && filled($profile->country)
            && filled($profile->city)
            && filled($profile->hiring_needs)
            && strlen(trim($profile->hiring_needs)) >= 20;

        return [
            'percent' => $percent,
            'status' => $status,
            'sections' => $sections,
            'next_section' => $nextSection,
            'is_catalog_ready' => $isCatalogReady,
        ];
    }

    private function emptyAssessment(): array
    {
        $sections = [
            'identity' => $this->buildSection(__('talenma.company.section_identity'), []),
            'presentation' => $this->buildSection(__('talenma.company.section_presentation'), []),
            'hiring' => $this->buildSection(__('talenma.company.section_hiring'), []),
            'contact' => $this->buildSection(__('talenma.company.section_contact'), []),
        ];

        return [
            'percent' => 0,
            'status' => 'starter',
            'sections' => $sections,
            'next_section' => 'identity',
            'is_catalog_ready' => false,
        ];
    }

    private function identitySection(CompanyProfile $profile): array
    {
        $items = [
            ['label' => __('talenma.company.check_logo'), 'done' => filled($profile->user?->avatar_path) || filled($profile->logo_path)],
            ['label' => __('talenma.company.check_sector'), 'done' => filled($profile->sector)],
            ['label' => __('talenma.company.check_employees'), 'done' => filled($profile->employee_count)],
            ['label' => __('talenma.company.check_location'), 'done' => filled($profile->country) && filled($profile->city)],
            ['label' => __('talenma.company.check_website'), 'done' => filled($profile->website)],
        ];

        return $this->buildSection(__('talenma.company.section_identity'), $items);
    }

    private function presentationSection(CompanyProfile $profile): array
    {
        $items = [
            ['label' => __('talenma.company.check_description'), 'done' => filled($profile->description) && strlen(trim($profile->description)) >= 50],
        ];

        return $this->buildSection(__('talenma.company.section_presentation'), $items);
    }

    private function hiringSection(CompanyProfile $profile): array
    {
        $items = [
            ['label' => __('talenma.company.check_hiring_needs'), 'done' => filled($profile->hiring_needs) && strlen(trim($profile->hiring_needs)) >= 20],
        ];

        return $this->buildSection(__('talenma.company.section_hiring'), $items);
    }

    private function contactSection(CompanyProfile $profile): array
    {
        $items = [
            ['label' => __('talenma.company.check_representative'), 'done' => filled($profile->representative_name)],
            ['label' => __('talenma.company.check_phone'), 'done' => filled($profile->phone)],
            ['label' => __('talenma.company.check_linkedin'), 'done' => filled($profile->linkedin_url)],
        ];

        return $this->buildSection(__('talenma.company.section_contact'), $items);
    }

    /**
     * @param  array<int, array{label: string, done: bool}>  $items
     * @return array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool}>}
     */
    private function buildSection(string $label, array $items): array
    {
        if ($items === []) {
            return [
                'label' => $label,
                'complete' => false,
                'percent' => 0,
                'items' => [],
            ];
        }

        $doneCount = collect($items)->where('done', true)->count();
        $percent = (int) round(($doneCount / count($items)) * 100);

        return [
            'label' => $label,
            'complete' => $doneCount === count($items),
            'percent' => $percent,
            'items' => $items,
        ];
    }
}
