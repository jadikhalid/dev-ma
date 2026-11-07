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

    public function test_company_overflow_menu_shows_unread_ping_when_inbox_has_unread(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);

        $talent = User::factory()->talent()->create();

        $conversation = \App\Models\Conversation::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'channel' => \App\Models\Conversation::CHANNEL_TALENT,
            'subject' => 'Mission unread ping',
            'last_message_at' => now(),
            'company_last_read_at' => null,
        ]);

        $conversation->messages()->create([
            'sender_user_id' => $talent->id,
            'body' => 'Bonjour, je vous contacte au sujet de la mission.',
        ]);

        $this->actingAs($company)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-inbox-overflow-ping', false)
            ->assertDontSee('data-inbox-overflow-ping" hidden', false);
    }
}
