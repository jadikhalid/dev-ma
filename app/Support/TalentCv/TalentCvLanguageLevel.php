<?php

namespace App\Support\TalentCv;

class TalentCvLanguageLevel
{
    public static function barPercent(string $level): int
    {
        $normalized = mb_strtolower(trim($level));

        if ($normalized === '') {
            return 55;
        }

        if (preg_match('/\b(c2|natif|native|maternel|langue maternelle)\b/u', $normalized)) {
            return 100;
        }

        if (preg_match('/\b(c1|courant|fluent|bilingue|bilingual)\b/u', $normalized)) {
            return 90;
        }

        if (preg_match('/\b(b2|avanc[eé]|advanced)\b/u', $normalized)) {
            return 80;
        }

        if (preg_match('/\b(b1|interm[eé]diaire|intermediate)\b/u', $normalized)) {
            return 65;
        }

        if (preg_match('/\b(a2|scolaire)\b/u', $normalized)) {
            return 50;
        }

        if (preg_match('/\b(a1|d[eé]butant|beginner|notions)\b/u', $normalized)) {
            return 35;
        }

        return 70;
    }
}
