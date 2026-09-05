<?php

namespace Tests\Unit\Services;

use App\Services\AtsCompatibilityScorer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AtsCompatibilityScorerTest extends TestCase
{
    #[Test]
    public function thin_text_scores_low(): void
    {
        $text = str_repeat('lorem ipsum ', 20);

        $result = (new AtsCompatibilityScorer)->scoreText($text);

        $this->assertLessThan(40, $result['score']);
        $this->assertGreaterThan(0, $result['issue_count']);
    }

    #[Test]
    public function rich_ats_friendly_text_scores_high(): void
    {
        $text = <<<'TXT'
Jean Dupont
Casablanca, Maroc
jean.dupont@email.com | +212 6 12 34 56 78
linkedin.com/in/jeandupont

Profil
Développeur Laravel senior avec 8 ans d'expérience sur des applications métier.

Compétences
Laravel, PHP, MySQL, Redis, Docker, AWS, Tailwind

Expérience
Senior Developer — TechScale SAS — 2020 - 2024
- Livré 12 modules métier (+35% de productivité)
- Réduit le temps de déploiement de 40%

Developer — StartupX — 2017 - 2020
- Construit 3 APIs consommées par 50k utilisateurs

Formation
Master Informatique — Université — 2016

Langues
Français, Anglais

Certifications
AWS Cloud Practitioner
TXT;

        $result = (new AtsCompatibilityScorer)->scoreText($text);

        $this->assertGreaterThanOrEqual(70, $result['score']);
        $this->assertLessThanOrEqual(100, $result['score']);
        $this->assertNotEmpty($result['findings']);
    }

    #[Test]
    public function emoji_fails_no_emoji_rule(): void
    {
        $text = "Profil motivé 🚀\nCompétences\nLaravel\nExpérience\n2020\nemail@test.com\n+212612345678\nCasablanca";

        $result = (new AtsCompatibilityScorer)->scoreText($text);
        $finding = collect($result['findings'])->firstWhere('id', 'no_emoji');

        $this->assertNotNull($finding);
        $this->assertSame('fail', $finding['status']);
    }
}
