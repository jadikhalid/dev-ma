<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicAppsLauncherGateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_hitting_ats_score_gate_is_sent_to_login(): void
    {
        $this->get(route('ats-score.gate'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function talent_passing_ats_score_gate_reaches_app(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('ats-score.gate'))
            ->assertRedirect(route('talent.ats-score.index'));
    }

    #[Test]
    public function company_passing_ats_score_gate_reaches_app(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('ats-score.gate'))
            ->assertRedirect(route('talent.ats-score.index'));
    }

    #[Test]
    public function company_member_can_open_ats_score(): void
    {
        $member = User::factory()->companyMember()->create();

        $this->actingAs($member)
            ->get(route('talent.ats-score.index'))
            ->assertOk();
    }

    #[Test]
    public function public_home_shows_apps_launcher_for_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('cv-builder.gate'), false)
            ->assertSee(route('ats-score.gate'), false);
    }
}
