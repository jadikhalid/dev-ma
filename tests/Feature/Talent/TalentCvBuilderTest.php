<?php

namespace Tests\Feature\Talent;

use App\Models\TalentCvDraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentCvBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_executive_template_preview_renders_timeline_layout(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->postJson(route('talent.cv-builder.preview'), [
                'template' => TalentCvDraft::TEMPLATE_EXECUTIVE,
                'locale' => 'fr',
                'data' => \App\Support\TalentCv\TalentCvDraftDefaults::sampleData('fr'),
            ], ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('timeline', false)
            ->assertSee('lang-track', false)
            ->assertSee('454545', false);
    }

    public function test_modern_template_preview_includes_photo_header(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->postJson(route('talent.cv-builder.preview'), [
                'template' => TalentCvDraft::TEMPLATE_MODERN,
                'locale' => 'fr',
                'data' => \App\Support\TalentCv\TalentCvDraftDefaults::sampleData('fr'),
            ], ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('photo-wrap', false)
            ->assertSee('social-link', false)
            ->assertSee('ecfdf5', false)
            ->assertSee('TechScale SAS', false);
    }

    public function test_preview_post_accepts_inline_data(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->postJson(route('talent.cv-builder.preview'), [
                'template' => TalentCvDraft::TEMPLATE_CLASSIC,
                'locale' => 'fr',
                'data' => [
                    'full_name' => 'Preview Test Name',
                    'headline' => 'Test headline',
                    'summary' => 'Test summary paragraph.',
                ],
            ], ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('Preview Test Name', false);
    }

    public function test_preview_post_returns_cv_html(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->postJson(route('talent.cv-builder.preview'), [
                'template' => TalentCvDraft::TEMPLATE_CLASSIC,
                'locale' => 'fr',
                'data' => \App\Support\TalentCv\TalentCvDraftDefaults::sampleData('fr'),
            ], ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('social-link', false)
            ->assertSee('Prénom Nom', false);
    }

    public function test_preview_get_returns_cv_html(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.preview'))
            ->assertOk()
            ->assertSee('Ingénieur · AI Software Engineering', false);
    }

    public function test_new_draft_is_prefilled_with_sample_content(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.index'))
            ->assertOk();

        $draft = TalentCvDraft::query()->where('user_id', $talent->id)->first();

        $this->assertNotNull($draft);
        $this->assertNotSame('', trim((string) ($draft->data['summary'] ?? '')));
        $this->assertNotSame('', trim((string) ($draft->data['experiences'][0]['title'] ?? '')));
        $this->assertGreaterThanOrEqual(2, count($draft->data['skill_groups'] ?? []));
        $this->assertGreaterThanOrEqual(3, count($draft->data['experiences'] ?? []));
        $this->assertNotSame('', trim((string) ($draft->data['photo_base64'] ?? '')));
    }

    public function test_approved_talent_can_open_cv_builder(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.index'))
            ->assertOk()
            ->assertSee(__('talenma.cv_builder.page_title'));
    }

    public function test_talent_can_save_profile_photo_source_in_draft(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->putJson(route('talent.cv-builder.update'), [
                'template' => TalentCvDraft::TEMPLATE_CLASSIC,
                'locale' => 'fr',
                'data' => array_merge(
                    \App\Support\TalentCv\TalentCvDraftDefaults::sampleData('fr'),
                    [
                        'photo_source' => 'profile',
                        'photo_base64' => '',
                    ],
                ),
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $draft = TalentCvDraft::query()->where('user_id', $talent->id)->first();

        $this->assertSame('profile', $draft->data['photo_source'] ?? null);
        $this->assertSame('', $draft->data['photo_base64'] ?? null);

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.index'))
            ->assertOk();

        $reloaded = TalentCvDraft::query()->where('user_id', $talent->id)->first();

        $this->assertSame('profile', $reloaded->data['photo_source'] ?? null);
        $this->assertSame('', $reloaded->data['photo_base64'] ?? null);
    }

    public function test_talent_can_save_custom_photo_in_draft(): void
    {
        $talent = User::factory()->talent()->create();
        $photoDataUri = 'data:image/jpeg;base64,'.base64_encode(str_repeat('a', 120_000));

        $this->actingAs($talent)
            ->putJson(route('talent.cv-builder.update'), [
                'template' => TalentCvDraft::TEMPLATE_CLASSIC,
                'locale' => 'fr',
                'data' => array_merge(
                    \App\Support\TalentCv\TalentCvDraftDefaults::sampleData('fr'),
                    [
                        'photo_source' => 'custom',
                        'photo_base64' => $photoDataUri,
                    ],
                ),
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $draft = TalentCvDraft::query()->where('user_id', $talent->id)->first();

        $this->assertSame($photoDataUri, $draft->data['photo_base64'] ?? null);

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.index'))
            ->assertOk();

        $reloaded = TalentCvDraft::query()->where('user_id', $talent->id)->first();

        $this->assertSame($photoDataUri, $reloaded->data['photo_base64'] ?? null);
    }

    public function test_talent_can_save_draft_and_export_pdf(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->putJson(route('talent.cv-builder.update'), [
                'template' => TalentCvDraft::TEMPLATE_MODERN,
                'locale' => 'en',
                'data' => [
                    'full_name' => 'Test Talent',
                    'headline' => 'Engineer',
                    'email' => 'test@example.com',
                    'summary' => 'Summary line for CV export test.',
                ],
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('talent_cv_drafts', [
            'user_id' => $talent->id,
            'template' => TalentCvDraft::TEMPLATE_MODERN,
            'locale' => 'en',
        ]);

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.export'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_company_cannot_access_cv_builder(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('talent.cv-builder.index'))
            ->assertForbidden();
    }
}
