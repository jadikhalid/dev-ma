<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TalentDashboardCvStatusCardsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function talent_dashboard_shows_editable_info_cards_with_empty_states(): void
    {
        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'work_modes' => null,
        ]);

        $this->actingAs($talent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-dashboard-talent-cv', false)
            ->assertSee('data-dashboard-talent-status', false)
            ->assertSee('data-dashboard-talent-profil', false)
            ->assertSee('data-dashboard-talent-contact', false)
            ->assertSee(__('talenma.dashboard.talent.cv_card_empty'), false)
            ->assertSee(__('talenma.dashboard.talent.status_card_empty_modes'), false)
            ->assertSee(__('talenma.dashboard.talent.profil_card_empty'), false)
            ->assertSee(__('talenma.dashboard.talent.contact_empty'), false);
    }

    #[Test]
    public function links_update_from_dashboard_returns_reload_flag(): void
    {
        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'country' => null,
            'city' => null,
        ]);

        $this->actingAs($talent)
            ->postJson(route('profile.details.update'), [
                'section' => 'links',
                'country' => 'ma',
                'city' => 'Casablanca',
                'phone' => '+212612345678',
                'from_dashboard' => '1',
            ])
            ->assertOk()
            ->assertJson([
                'reload' => true,
            ]);

        $profile = $talent->fresh()->profile;
        $this->assertSame('ma', $profile->country);
        $this->assertSame('Casablanca', $profile->city);
        $this->assertSame('+212612345678', $profile->phone);
    }

    #[Test]
    public function availability_update_from_dashboard_returns_reload_flag(): void
    {
        $talent = User::factory()->talent()->create();
        $talent->profile()->create([
            'availability' => Profile::STATUS_BUSY,
            'work_modes' => ['local'],
        ]);

        $this->actingAs($talent)
            ->postJson(route('profile.details.update'), [
                'section' => 'availability',
                'availability' => Profile::STATUS_AVAILABLE,
                'work_modes' => ['remote'],
                'from_dashboard' => '1',
            ])
            ->assertOk()
            ->assertJson([
                'reload' => true,
            ]);

        $profile = $talent->fresh()->profile;
        $this->assertSame(Profile::STATUS_AVAILABLE, $profile->availability);
        $this->assertSame(['remote'], $profile->work_modes);
    }
}
