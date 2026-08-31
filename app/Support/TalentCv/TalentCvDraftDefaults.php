<?php

namespace App\Support\TalentCv;

use App\Models\User;

class TalentCvDraftDefaults
{
    /** @return array<string, mixed> */
    public static function emptyStructure(): array
    {
        return [
            'full_name' => '',
            'headline' => '',
            'email' => '',
            'phone' => '',
            'city' => '',
            'linkedin_url' => '',
            'github_url' => '',
            'portfolio_url' => '',
            'summary' => '',
            'skill_groups' => [
                ['label' => '', 'items' => ''],
            ],
            'experiences' => [
                [
                    'title' => '',
                    'company' => '',
                    'location' => '',
                    'start' => '',
                    'end' => '',
                    'current' => false,
                    'bullets' => [''],
                ],
            ],
            'education' => [
                ['degree' => '', 'school' => '', 'year' => ''],
            ],
            'languages' => [
                ['name' => '', 'level' => ''],
            ],
            'certifications' => [''],
            'availability_line' => '',
            'photo_source' => 'sample',
            'photo_base64' => '',
        ];
    }

    /** @return array<string, mixed> */
    public static function sampleData(string $locale = 'fr'): array
    {
        /** @var array<string, mixed> $sample */
        $sample = __('talenma.cv_builder.sample', [], $locale);

        $merged = self::merge(is_array($sample) ? $sample : []);

        if (self::isBlank($merged['photo_base64'] ?? null)) {
            $merged['photo_base64'] = TalentCvSampleAvatar::dataUri();
        }

        $merged['photo_source'] = 'sample';

        return $merged;
    }

    /** @return array<string, mixed> */
    public static function fromUser(User $user, string $locale = 'fr'): array
    {
        return self::sampleData($locale);
    }

    /**
     * Fill empty draft fields with generic sample content (keeps user edits).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mergeEmptyWithSample(array $data, string $locale = 'fr'): array
    {
        $sample = self::sampleData($locale);
        $merged = self::merge($data);

        foreach (['full_name', 'headline', 'email', 'phone', 'city', 'linkedin_url', 'github_url', 'portfolio_url', 'summary', 'availability_line'] as $key) {
            if (self::isBlank($merged[$key] ?? null)) {
                $merged[$key] = $sample[$key] ?? '';
            }
        }

        if (! self::sectionHasSkillGroups($merged['skill_groups'] ?? [])) {
            $merged['skill_groups'] = $sample['skill_groups'];
        }

        if (! self::sectionHasExperiences($merged['experiences'] ?? [])) {
            $merged['experiences'] = $sample['experiences'];
        }

        if (! self::sectionHasEducation($merged['education'] ?? [])) {
            $merged['education'] = $sample['education'];
        }

        if (! self::sectionHasLanguages($merged['languages'] ?? [])) {
            $merged['languages'] = $sample['languages'];
        }

        if (! self::sectionHasCertifications($merged['certifications'] ?? [])) {
            $merged['certifications'] = $sample['certifications'];
        }

        if (($merged['photo_source'] ?? 'sample') !== 'profile' && self::isBlank($merged['photo_base64'] ?? null)) {
            $merged['photo_base64'] = $sample['photo_base64'] ?? TalentCvSampleAvatar::dataUri();
            $merged['photo_source'] = 'sample';
        }

        return self::merge($merged);
    }

    private static function isBlank(mixed $value): bool
    {
        return trim((string) $value) === '';
    }

    /** @param  list<array<string, mixed>>  $groups */
    private static function sectionHasSkillGroups(array $groups): bool
    {
        foreach ($groups as $group) {
            if (! self::isBlank($group['label'] ?? null) || ! self::isBlank($group['items'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /** @param  list<array<string, mixed>>  $items */
    private static function sectionHasExperiences(array $items): bool
    {
        foreach ($items as $item) {
            if (! self::isBlank($item['title'] ?? null) || ! self::isBlank($item['company'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /** @param  list<array<string, mixed>>  $items */
    private static function sectionHasEducation(array $items): bool
    {
        foreach ($items as $item) {
            if (! self::isBlank($item['degree'] ?? null) || ! self::isBlank($item['school'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /** @param  list<array<string, mixed>>  $items */
    private static function sectionHasLanguages(array $items): bool
    {
        foreach ($items as $item) {
            if (! self::isBlank($item['name'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /** @param  list<mixed>  $items */
    private static function sectionHasCertifications(array $items): bool
    {
        foreach ($items as $item) {
            if (! self::isBlank($item)) {
                return true;
            }
        }

        return false;
    }

    /** @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    public static function merge(array $incoming): array
    {
        $base = self::emptyStructure();

        $merged = array_replace_recursive($base, $incoming);

        $merged['skill_groups'] = self::normalizeList($merged['skill_groups'] ?? [], ['label' => '', 'items' => '']);
        $merged['experiences'] = self::normalizeExperiences($merged['experiences'] ?? []);
        $merged['education'] = self::normalizeList($merged['education'] ?? [], ['degree' => '', 'school' => '', 'year' => '']);
        $merged['languages'] = self::normalizeList($merged['languages'] ?? [], ['name' => '', 'level' => '']);
        $merged['certifications'] = self::normalizeStrings($merged['certifications'] ?? ['']);

        if (isset($incoming['photo_source'])) {
            $source = (string) $incoming['photo_source'];
            $merged['photo_source'] = in_array($source, ['custom', 'profile', 'sample'], true) ? $source : 'sample';
        }

        if (isset($incoming['photo_base64'])) {
            $merged['photo_base64'] = is_string($incoming['photo_base64']) ? $incoming['photo_base64'] : '';
        }

        if (($merged['photo_source'] ?? 'sample') === 'profile') {
            $merged['photo_base64'] = '';
        } elseif (! self::isBlank($merged['photo_base64'] ?? null)) {
            $merged['photo_source'] = 'custom';
        } elseif (! isset($merged['photo_source'])) {
            $merged['photo_source'] = 'sample';
        }

        return $merged;
    }

    /** @param  list<mixed>  $items
     * @param  array<string, mixed>  $emptyRow
     * @return list<array<string, mixed>>
     */
    private static function normalizeList(array $items, array $emptyRow): array
    {
        $rows = collect($items)
            ->filter(fn ($row) => is_array($row))
            ->map(fn (array $row) => array_merge($emptyRow, $row))
            ->values()
            ->all();

        return $rows !== [] ? $rows : [$emptyRow];
    }

    /** @param  list<mixed>  $items
     * @return list<array<string, mixed>>
     */
    private static function normalizeExperiences(array $items): array
    {
        $empty = [
            'title' => '',
            'company' => '',
            'location' => '',
            'start' => '',
            'end' => '',
            'current' => false,
            'bullets' => [''],
        ];

        $rows = collect($items)
            ->filter(fn ($row) => is_array($row))
            ->map(function (array $row) use ($empty) {
                $merged = array_merge($empty, $row);
                $merged['current'] = (bool) ($merged['current'] ?? false);
                $bullets = $merged['bullets'] ?? [''];
                $merged['bullets'] = self::normalizeStrings(is_array($bullets) ? $bullets : ['']);

                return $merged;
            })
            ->values()
            ->all();

        return $rows !== [] ? $rows : [$empty];
    }

    /** @param  list<mixed>  $items
     * @return list<string>
     */
    private static function normalizeStrings(array $items): array
    {
        $strings = collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn (string $s) => $s !== '')
            ->values()
            ->all();

        return $strings !== [] ? $strings : [''];
    }
}
