<?php

namespace App\Services;

use App\Models\Profile;

class TalentProfileCompletionService
{
    /**
     * @return array{
     *     percent: int,
     *     status: string,
     *     sections: array<string, array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool}>}>,
     *     next_section: string|null,
     *     is_catalog_ready: bool
     * }
     */
    public function assess(?Profile $profile): array
    {
        if (! $profile) {
            return $this->emptyAssessment();
        }

        $profile->loadMissing('documents');

        $sections = [
            'profession' => $this->professionSection($profile),
            'presentation' => $this->presentationSection($profile),
            'availability' => $this->availabilitySection($profile),
            'links' => $this->linksSection($profile),
        ];

        $requiredPercent = (int) round(collect($sections)
            ->only(['profession', 'presentation', 'availability'])
            ->avg(fn (array $section) => $section['percent']));

        $percent = min(100, (int) round(($requiredPercent * 0.85) + ($sections['links']['percent'] * 0.15)));

        $nextSection = null;

        foreach (['profession', 'presentation', 'availability', 'links'] as $key) {
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

        $isCatalogReady = $sections['profession']['complete']
            && $sections['presentation']['complete']
            && $sections['availability']['complete'];

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
            'profession' => $this->buildSection(__('talenma.dashboard.talent.section_profession'), []),
            'presentation' => $this->buildSection(__('talenma.dashboard.talent.section_presentation'), []),
            'availability' => $this->buildSection(__('talenma.dashboard.talent.section_availability'), []),
            'links' => $this->buildSection(__('talenma.dashboard.talent.section_links'), []),
        ];

        return [
            'percent' => 0,
            'status' => 'starter',
            'sections' => $sections,
            'next_section' => 'profession',
            'is_catalog_ready' => false,
        ];
    }

    private function professionSection(Profile $profile): array
    {
        $items = [
            ['label' => __('talenma.dashboard.talent.check_sector'), 'done' => (bool) $profile->profession_sector_id],
            ['label' => __('talenma.dashboard.talent.check_registration_description'), 'done' => filled($profile->registration_description)],
            ['label' => __('talenma.dashboard.talent.check_documents'), 'done' => $profile->documents->isNotEmpty()],
            ['label' => __('talenma.dashboard.talent.check_profession'), 'done' => (bool) $profile->profession_id],
            ['label' => __('talenma.dashboard.talent.check_specialization'), 'done' => filled($profile->specialization)],
            ['label' => __('talenma.dashboard.talent.check_title'), 'done' => filled($profile->title)],
        ];

        return $this->buildSection(__('talenma.dashboard.talent.section_profession'), $items);
    }

    private function presentationSection(Profile $profile): array
    {
        $items = [
            ['label' => __('talenma.dashboard.talent.check_bio'), 'done' => filled($profile->bio) && strlen(trim($profile->bio)) >= 30],
            ['label' => __('talenma.dashboard.talent.check_experience'), 'done' => $profile->experience_years !== null],
            ['label' => __('talenma.dashboard.talent.check_education'), 'done' => filled($profile->education_level)],
            ['label' => __('talenma.dashboard.talent.check_skills'), 'done' => is_array($profile->skills) && count($profile->skills) > 0],
        ];

        return $this->buildSection(__('talenma.dashboard.talent.section_presentation'), $items);
    }

    private function availabilitySection(Profile $profile): array
    {
        $items = [
            ['label' => __('talenma.dashboard.talent.check_location'), 'done' => filled($profile->city) && filled($profile->country)],
            ['label' => __('talenma.dashboard.talent.check_rate'), 'done' => filled($profile->daily_rate_eur)],
            ['label' => __('talenma.dashboard.talent.check_availability'), 'done' => filled($profile->availability)],
            ['label' => __('talenma.dashboard.talent.check_work_modes'), 'done' => is_array($profile->work_modes) && count($profile->work_modes) > 0],
            ['label' => __('talenma.dashboard.talent.check_languages'), 'done' => is_array($profile->languages) && count($profile->languages) > 0],
        ];

        return $this->buildSection(__('talenma.dashboard.talent.section_availability'), $items);
    }

    private function linksSection(Profile $profile): array
    {
        $items = [
            ['label' => 'LinkedIn', 'done' => filled($profile->linkedin_url)],
            ['label' => __('talenma.talent.portfolio'), 'done' => filled($profile->portfolio_url)],
            ['label' => __('talenma.talent.phone'), 'done' => filled($profile->phone)],
        ];

        return $this->buildSection(__('talenma.dashboard.talent.section_links'), $items);
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
