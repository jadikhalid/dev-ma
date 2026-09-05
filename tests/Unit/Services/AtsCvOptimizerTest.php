<?php

namespace Tests\Unit\Services;

use App\Services\AtsCompatibilityScorer;
use App\Services\AtsCvOptimizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AtsCvOptimizerTest extends TestCase
{
    #[Test]
    public function optimize_raises_score_near_ats_friendly_target(): void
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

        $this->assertGreaterThan($before['score'], $optimized['result']['score']);
        $this->assertGreaterThanOrEqual(90, $optimized['result']['score']);
        $this->assertStringContainsString('Profil', $optimized['text']);
        $this->assertStringContainsString('Compétences', $optimized['text']);
        $this->assertStringContainsString('Expérience', $optimized['text']);
        $this->assertStringNotContainsString('🚀', $optimized['text']);
    }
}
