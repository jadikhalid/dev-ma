<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingCvPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_cv_preview_renders_sample_content(): void
    {
        $this->get(route('marketing.cv-preview', ['template' => 'classic']))
            ->assertOk()
            ->assertSee('Prénom Nom', false);

        $this->get(route('marketing.cv-preview', ['template' => 'modern']))
            ->assertOk()
            ->assertSee('social-link', false);

        $this->get(route('marketing.cv-preview', ['template' => 'executive']))
            ->assertOk()
            ->assertSee('timeline', false)
            ->assertSee('lang-track', false);
    }

    public function test_invalid_cv_preview_template_returns_not_found(): void
    {
        $this->get('/outils/apercu-cv/invalid')->assertNotFound();
    }
}
