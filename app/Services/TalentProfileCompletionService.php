<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileDocument;

class TalentProfileCompletionService
{
    /**
     * @return array{
     *     percent: int,
     *     status: string,
     *     sections: array<string, array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool, required: bool}>}>,
     *     next_section: string|null,
     *     is_catalog_ready: bool,
     *     done_count: int,
     *     total_count: int
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
            'documents' => $this->documentsSection($profile),
            'links' => $this->linksSection($profile),
        ];

        $allItems = collect($sections)->flatMap(fn (array $section) => $section['items']);
        $totalCount = $allItems->count();
        $doneCount = $allItems->where('done', true)->count();
        $percent = $totalCount > 0
            ? (int) round(($doneCount / $totalCount) * 100)
            : 0;

        $nextSection = null;

        foreach (array_keys($sections) as $key) {
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

        $requiredItems = $allItems->where('required', true);
        $isCatalogReady = $requiredItems->isNotEmpty()
            && $requiredItems->every(fn (array $item) => $item['done']);

        return [
            'percent' => $percent,
            'status' => $status,
            'sections' => $sections,
            'next_section' => $nextSection,
            'is_catalog_ready' => $isCatalogReady,
            'done_count' => $doneCount,
            'total_count' => $totalCount,
        ];
    }

    private function emptyAssessment(): array
    {
        $sections = [
            'profession' => $this->buildSection(__('talenma.dashboard.talent.section_profession'), []),
            'presentation' => $this->buildSection(__('talenma.dashboard.talent.section_presentation'), []),
            'availability' => $this->buildSection(__('talenma.dashboard.talent.section_availability'), []),
            'documents' => $this->buildSection(__('talenma.dashboard.talent.section_documents'), []),
            'links' => $this->buildSection(__('talenma.dashboard.talent.section_links'), []),
        ];

        return [
            'percent' => 0,
            'status' => 'starter',
            'sections' => $sections,
            'next_section' => 'profession',
            'is_catalog_ready' => false,
            'done_count' => 0,
            'total_count' => 0,
        ];
    }

    private function professionSection(Profile $profile): array
    {
        return $this->buildSection(__('talenma.dashboard.talent.section_profession'), [
            ['label' => __('talenma.dashboard.talent.check_sector'), 'done' => (bool) $profile->profession_sector_id, 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_profession'), 'done' => (bool) $profile->profession_id, 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_specialization'), 'done' => filled($profile->specialization), 'required' => true],
        ]);
    }

    private function presentationSection(Profile $profile): array
    {
        return $this->buildSection(__('talenma.dashboard.talent.section_presentation'), [
            ['label' => __('talenma.dashboard.talent.check_bio'), 'done' => filled($profile->bio) && strlen(trim((string) $profile->bio)) >= 30, 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_experience'), 'done' => $profile->experience_years !== null, 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_education'), 'done' => filled($profile->education_level), 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_skills'), 'done' => is_array($profile->skills) && count($profile->skills) > 0, 'required' => false],
        ]);
    }

    private function availabilitySection(Profile $profile): array
    {
        return $this->buildSection(__('talenma.dashboard.talent.section_availability'), [
            ['label' => __('talenma.dashboard.talent.check_location'), 'done' => filled($profile->city) && filled($profile->country), 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_availability'), 'done' => filled($profile->availability), 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_work_modes'), 'done' => is_array($profile->work_modes) && count($profile->work_modes) > 0, 'required' => true],
            ['label' => __('talenma.dashboard.talent.check_languages'), 'done' => is_array($profile->languages) && count($profile->languages) > 0, 'required' => true],
        ]);
    }

    private function documentsSection(Profile $profile): array
    {
        return $this->buildSection(__('talenma.dashboard.talent.section_documents'), [
            [
                'label' => __('talenma.dashboard.talent.check_cv'),
                'done' => $profile->documents->contains(
                    fn (ProfileDocument $document) => $document->document_type === ProfileDocument::TYPE_CV
                ),
                'required' => false,
            ],
        ]);
    }

    private function linksSection(Profile $profile): array
    {
        return $this->buildSection(__('talenma.dashboard.talent.section_links'), [
            ['label' => 'LinkedIn', 'done' => filled($profile->linkedin_url), 'required' => false],
            ['label' => 'GitHub', 'done' => filled($profile->github_url), 'required' => false],
            ['label' => __('talenma.talent.portfolio'), 'done' => filled($profile->portfolio_url), 'required' => false],
            ['label' => __('talenma.talent.phone'), 'done' => filled($profile->phone), 'required' => false],
        ]);
    }

    /**
     * @param  array<int, array{label: string, done: bool, required?: bool}>  $items
     * @return array{label: string, complete: bool, percent: int, items: array<int, array{label: string, done: bool, required: bool}>}
     */
    private function buildSection(string $label, array $items): array
    {
        $normalized = array_map(function (array $item) {
            return [
                'label' => $item['label'],
                'done' => (bool) $item['done'],
                'required' => (bool) ($item['required'] ?? false),
            ];
        }, $items);

        if ($normalized === []) {
            return [
                'label' => $label,
                'complete' => false,
                'percent' => 0,
                'items' => [],
            ];
        }

        $doneCount = collect($normalized)->where('done', true)->count();
        $requiredItems = collect($normalized)->where('required', true);
        $requiredComplete = $requiredItems->isEmpty()
            || $requiredItems->every(fn (array $item) => $item['done']);

        return [
            'label' => $label,
            'complete' => $requiredComplete && $doneCount === count($normalized),
            'percent' => (int) round(($doneCount / count($normalized)) * 100),
            'items' => $normalized,
        ];
    }
}
