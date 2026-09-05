<?php

namespace Tests\Feature\Talent;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TalentAtsScoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function approved_talent_can_view_upload_page(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('talent.ats-score.index'))
            ->assertOk()
            ->assertSee('ATS Score', false)
            ->assertSee(__('talenma.ats_score.upload_title'), false)
            ->assertSee(__('talenma.ats_score.upload_cta'), false);
    }

    #[Test]
    public function talent_can_analyze_uploaded_txt_cv(): void
    {
        Storage::fake('local');

        $talent = User::factory()->talent()->create();

        $content = <<<'TXT'
Sara Benali
Rabat, Maroc
sara.benali@email.com
+212 6 98 76 54 32
https://linkedin.com/in/sarabenali

Profil
Product designer avec 6 ans d'expérience.

Compétences
Figma, UX research, Design systems

Expérience
Lead Designer — Agency — 2019 - 2024
- Amélioré le taux de conversion de 28%
- Mentoring de 4 designers

Formation
Master Design — 2018

Langues
Français, Anglais

Certifications
Google UX Certificate
TXT;

        $file = UploadedFile::fake()->createWithContent('cv-sara.txt', $content);

        $this->actingAs($talent)
            ->post(route('talent.ats-score.analyze'), ['cv' => $file])
            ->assertRedirect(route('talent.ats-score.index'));

        $this->actingAs($talent)
            ->get(route('talent.ats-score.index'))
            ->assertOk()
            ->assertSee('cv-sara.txt', false)
            ->assertSee(__('talenma.ats_score.checklist_title'), false);
    }

    #[Test]
    public function talent_can_optimize_uploaded_cv_for_free(): void
    {
        $talent = User::factory()->talent()->create();

        $content = <<<'TXT'
Sara Benali
Rabat
sara.benali@email.com
+212698765432
linkedin.com/in/sarabenali

Quelques lignes sur mon parcours design produit.
Figma UX UI research
Lead Designer Agency 2019 2024
Ameliore conversion
Master Design 2018
TXT;

        $file = UploadedFile::fake()->createWithContent('cv-sara.txt', $content);

        $this->actingAs($talent)
            ->post(route('talent.ats-score.analyze'), ['cv' => $file])
            ->assertRedirect(route('talent.ats-score.index'));

        $this->actingAs($talent)
            ->post(route('talent.ats-score.optimize'))
            ->assertRedirect(route('talent.ats-score.index'));

        $this->actingAs($talent)
            ->get(route('talent.ats-score.index'))
            ->assertOk()
            ->assertSee(__('talenma.ats_score.optimize_free_badge'), false)
            ->assertSee(__('talenma.ats_score.download_optimized'), false)
            ->assertSee('Compétences', false)
            ->assertSee('Expérience', false);

        $this->actingAs($talent)
            ->get(route('talent.ats-score.download'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8');
    }

    #[Test]
    public function rejects_empty_looking_file(): void
    {
        $talent = User::factory()->talent()->create();
        $file = UploadedFile::fake()->createWithContent('empty.txt', 'too short');

        $this->actingAs($talent)
            ->from(route('talent.ats-score.index'))
            ->post(route('talent.ats-score.analyze'), ['cv' => $file])
            ->assertRedirect(route('talent.ats-score.index'))
            ->assertSessionHasErrors('cv');
    }

    #[Test]
    public function company_owner_can_access_ats_score(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('talent.ats-score.index'))
            ->assertOk()
            ->assertSee('ATS Score', false);
    }

    #[Test]
    public function guest_is_redirected_to_login(): void
    {
        $this->get(route('talent.ats-score.index'))
            ->assertRedirect(route('login'));
    }
}
