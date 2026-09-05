<?php

namespace App\Services;

/**
 * Deterministic ATS-friendliness heuristics against extracted CV plain text.
 * Independent from the in-app CV builder. Targets strict, text-first parsers.
 */
class AtsCompatibilityScorer
{
    /**
     * @return array{
     *     score: int,
     *     max_points: int,
     *     earned_points: int,
     *     findings: list<array{id: string, status: string, severity: string, earned: int, max: int}>,
     *     passed_count: int,
     *     issue_count: int,
     *     char_count: int
     * }
     */
    public function scoreText(string $text): array
    {
        $normalized = trim($text);
        $lower = mb_strtolower($normalized);
        $lines = preg_split("/\n+/u", $normalized) ?: [];

        $findings = [];
        $findings[] = $this->checkExtractable($normalized);
        $findings[] = $this->checkEmail($normalized);
        $findings[] = $this->checkPhone($normalized);
        $findings[] = $this->checkLocation($lower);
        $findings[] = $this->checkLinks($lower);
        $findings[] = $this->checkSection($lower, 'summary_section', [
            'profil', 'profile', 'summary', 'résumé', 'resume', 'à propos', 'about me', 'objectif', 'objective',
        ], 8, 'high');
        $findings[] = $this->checkSection($lower, 'skills_section', [
            'compétences', 'competences', 'skills', 'technologies', 'tech stack', 'outils', 'tools',
        ], 12, 'high');
        $findings[] = $this->checkSection($lower, 'experience_section', [
            'expérience', 'experience', 'expériences professionnelles', 'professional experience',
            'emploi', 'employment', 'work history', 'parcours professionnel',
        ], 12, 'critical');
        $findings[] = $this->checkSection($lower, 'education_section', [
            'formation', 'éducation', 'education', 'diplôme', 'diplomes', 'academic', 'études', 'etudes',
        ], 6, 'medium');
        $findings[] = $this->checkSection($lower, 'languages_section', [
            'langues', 'languages', 'language skills',
        ], 2, 'low');
        $findings[] = $this->checkSection($lower, 'certifications_section', [
            'certification', 'certifications', 'certificat', 'accréditation',
        ], 2, 'low');
        $findings[] = $this->checkDates($normalized);
        $findings[] = $this->checkBullets($lines);
        $findings[] = $this->checkMeasurable($normalized);
        $findings[] = $this->checkNoEmoji($normalized);
        $findings[] = $this->checkLength($normalized);
        $findings[] = $this->checkContactBlock($normalized);

        $earned = (int) array_sum(array_column($findings, 'earned'));
        $max = (int) array_sum(array_column($findings, 'max'));
        $score = $max > 0 ? (int) round(($earned / $max) * 100) : 0;

        $passed = count(array_filter($findings, fn (array $f) => $f['status'] === 'pass'));
        $issues = count(array_filter($findings, fn (array $f) => in_array($f['status'], ['fail', 'partial', 'warn'], true)));

        return [
            'score' => max(0, min(100, $score)),
            'max_points' => $max,
            'earned_points' => $earned,
            'findings' => $findings,
            'passed_count' => $passed,
            'issue_count' => $issues,
            'char_count' => mb_strlen($normalized),
        ];
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkExtractable(string $text): array
    {
        $len = mb_strlen($text);
        if ($len >= 400) {
            return $this->finding('text_extractable', 'pass', 'critical', 10, 10);
        }
        if ($len >= AtsCvTextExtractor::MIN_CHARS) {
            return $this->finding('text_extractable', 'partial', 'critical', 5, 10);
        }

        return $this->finding('text_extractable', 'fail', 'critical', 0, 10);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkEmail(string $text): array
    {
        $ok = (bool) preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text);

        return $this->finding('email', $ok ? 'pass' : 'fail', 'critical', $ok ? 10 : 0, 10);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkPhone(string $text): array
    {
        $ok = (bool) preg_match('/(?:\+|00)?[\d\s().\-]{8,}\d/', $text);

        return $this->finding('phone', $ok ? 'pass' : 'fail', 'high', $ok ? 6 : 0, 6);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkLocation(string $lower): array
    {
        $ok = (bool) preg_match(
            '/\b(casablanca|rabat|marrakech|marrakesh|tanger|tangier|f[eè]s|fez|agadir|mekn[eè]s|oujda|paris|lyon|marseille|lille|toulouse|nantes|bordeaux|remote|t[eé]l[eé]travail|maroc|morocco|france|belgium|belgique|canada|dubai|london|madrid)\b/u',
            $lower
        ) || (bool) preg_match('/\b\d{5}\b/', $lower);

        return $this->finding('city', $ok ? 'pass' : 'fail', 'medium', $ok ? 4 : 0, 4);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkLinks(string $lower): array
    {
        $ok = str_contains($lower, 'linkedin.com')
            || str_contains($lower, 'github.com')
            || str_contains($lower, 'gitlab.com')
            || (bool) preg_match('/https?:\/\/[^\s]+/u', $lower);

        return $this->finding('links', $ok ? 'pass' : 'fail', 'medium', $ok ? 4 : 0, 4);
    }

    /**
     * @param  list<string>  $keywords
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkSection(string $lower, string $id, array $keywords, int $max, string $severity): array
    {
        foreach ($keywords as $keyword) {
            if (str_contains($lower, mb_strtolower($keyword))) {
                return $this->finding($id, 'pass', $severity, $max, $max);
            }
        }

        return $this->finding($id, 'fail', $severity, 0, $max);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkDates(string $text): array
    {
        preg_match_all('/\b(?:19|20)\d{2}\b/u', $text, $years);
        $count = count($years[0] ?? []);

        if ($count >= 4) {
            return $this->finding('experience_dates', 'pass', 'high', 6, 6);
        }
        if ($count >= 2) {
            return $this->finding('experience_dates', 'partial', 'high', 3, 6);
        }

        return $this->finding('experience_dates', 'fail', 'high', 0, 6);
    }

    /**
     * @param  list<string>  $lines
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkBullets(array $lines): array
    {
        $bullets = 0;
        foreach ($lines as $line) {
            $trim = ltrim($line);
            if (preg_match('/^([•●▪◦\-–—*➤➢]|\\d+[.)])\s+\S/u', $trim)) {
                $bullets++;
            }
        }

        if ($bullets >= 6) {
            return $this->finding('experience_bullets', 'pass', 'high', 8, 8);
        }
        if ($bullets >= 3) {
            return $this->finding('experience_bullets', 'partial', 'high', 4, 8);
        }

        return $this->finding('experience_bullets', 'fail', 'high', 0, 8);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkMeasurable(string $text): array
    {
        preg_match_all('/\b\d+(?:[.,]\d+)?\s*%|\b\d{2,}\b/u', $text, $matches);
        $count = count($matches[0] ?? []);

        if ($count >= 6) {
            return $this->finding('measurable_bullets', 'pass', 'medium', 4, 4);
        }
        if ($count >= 2) {
            return $this->finding('measurable_bullets', 'partial', 'medium', 2, 4);
        }

        return $this->finding('measurable_bullets', 'fail', 'medium', 0, 4);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkNoEmoji(string $text): array
    {
        $hasEmoji = (bool) preg_match('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', $text);

        return $this->finding('no_emoji', $hasEmoji ? 'fail' : 'pass', 'medium', $hasEmoji ? 0 : 4, 4);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkLength(string $text): array
    {
        $len = mb_strlen($text);
        if ($len >= 1200 && $len <= 12000) {
            return $this->finding('length', 'pass', 'medium', 6, 6);
        }
        if ($len >= 600) {
            return $this->finding('length', 'partial', 'medium', 3, 6);
        }

        return $this->finding('length', 'fail', 'medium', 0, 6);
    }

    /**
     * Contact details near the top of the document (ATS-friendly header).
     *
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function checkContactBlock(string $text): array
    {
        $head = mb_substr($text, 0, 600);
        $hasEmail = (bool) preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $head);
        $hasPhone = (bool) preg_match('/(?:\+|00)?[\d\s().\-]{8,}\d/', $head);
        $hits = ($hasEmail ? 1 : 0) + ($hasPhone ? 1 : 0);

        if ($hits === 2) {
            return $this->finding('contact_header', 'pass', 'high', 6, 6);
        }
        if ($hits === 1) {
            return $this->finding('contact_header', 'partial', 'high', 3, 6);
        }

        return $this->finding('contact_header', 'fail', 'high', 0, 6);
    }

    /**
     * @return array{id: string, status: string, severity: string, earned: int, max: int}
     */
    private function finding(string $id, string $status, string $severity, int $earned, int $max): array
    {
        return [
            'id' => $id,
            'status' => $status,
            'severity' => $severity,
            'earned' => $earned,
            'max' => $max,
        ];
    }
}
