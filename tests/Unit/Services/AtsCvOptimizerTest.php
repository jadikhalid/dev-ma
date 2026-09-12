<?php

namespace Tests\Unit\Services;

use App\Services\AtsCompatibilityScorer;
use App\Services\AtsCvOptimizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AtsCvOptimizerTest extends TestCase
{
    #[Test]
    public function optimize_returns_concrete_suggestions_not_a_full_cv(): void
    {
        $source = <<<'TXT'
Ali Karim
ali@mail.com
Design et produit
quelques missions en 2021
TXT;

        $scorer = new AtsCompatibilityScorer;
        $before = $scorer->scoreText($source);
        $optimized = (new AtsCvOptimizer($scorer))->optimize($source, $before, 'fr');

        $this->assertNotEmpty($optimized['suggestions']);
        $this->assertArrayHasKey('action', $optimized['suggestions'][0]);
        $this->assertArrayHasKey('example', $optimized['suggestions'][0]);
        $this->assertGreaterThanOrEqual($before['score'], $optimized['result']['score']);
        $this->assertStringContainsString('Suggestions ATS', $optimized['text']);
        $this->assertStringNotContainsString('Résumé complémentaire ATS', $optimized['text']);
    }

    #[Test]
    public function emoji_finding_produces_remove_suggestion(): void
    {
        $text = "Profil motivé 🚀\nCompétences\nLaravel\nExpérience\n2020 2018\nemail@test.com\n+212612345678\nCasablanca\nlinkedin.com/in/x";
        $scorer = new AtsCompatibilityScorer;
        $result = $scorer->scoreText($text);
        $optimized = (new AtsCvOptimizer($scorer))->optimize($text, $result, 'fr');

        $emojiTip = collect($optimized['suggestions'])->firstWhere('id', 'no_emoji');
        $this->assertNotNull($emojiTip);
        $this->assertSame('remove', $emojiTip['action']);
    }
}
