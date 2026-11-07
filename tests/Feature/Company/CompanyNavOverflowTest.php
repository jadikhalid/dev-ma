<?php

namespace Tests\Feature\Company;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyNavOverflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_messages_is_forced_into_desktop_overflow_menu(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('sourcing.index'))
            ->assertOk()
            ->assertSee('data-nav-overflow-always', false)
            ->assertSee('data-nav-more-button', false)
            ->assertSee(__('talenma.nav.messages'))
            ->assertSee(__('talenma.nav.sourcing'))
            ->assertDontSee('data-nav-keep=', false);
    }

    public function test_company_dashboard_still_exposes_primary_company_links(): void
    {
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('talenma.nav.sourcing'))
            ->assertSee(__('talenma.nav.direct_hire'))
            ->assertSee('data-nav-overflow-always', false);
    }
}
