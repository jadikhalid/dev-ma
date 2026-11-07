<?php

namespace Tests\Feature;

use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxComposeTest extends TestCase
{
    use RefreshDatabase;

    private function createContactableTalent(array $overrides = []): User
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it-digital-inbox',
            'name_fr' => 'IT',
            'name_en' => 'IT',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-laravel-inbox',
            'name_fr' => 'Développeur Laravel',
            'name_en' => 'Laravel developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $talent = User::factory()->talent()->create(array_merge([
            'first_name' => 'Amine',
            'last_name' => 'Benali',
            'name' => 'Amine Benali',
            'email' => 'amine.benali@example.com',
        ], $overrides));

        $profile = $talent->profile()->create([
            'profession_id' => $profession->id,
            'profession_sector_id' => $sector->id,
            'bio' => 'Développeur Laravel avec plusieurs années d’expérience sur des applications métier.',
            'experience_years' => 5,
        ]);

        $profile->documents()->create([
            'document_type' => ProfileDocument::TYPE_CV,
            'language' => 'fr',
            'path' => 'profile-documents/cv-amine.pdf',
            'original_name' => 'cv-amine.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ]);

        return $talent->fresh('profile');
    }

    public function test_company_can_search_talents_for_inbox_compose(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $response = $this->actingAs($company)->getJson(route('inbox.talent-suggestions', [
            'q' => 'Amine',
        ]));

        $response->assertOk();
        $response->assertJsonPath('results.0.id', $talent->id);
        $this->assertNotEmpty($response->json('results.0.label'));
    }

    public function test_talent_cannot_search_inbox_talents(): void
    {
        $talent = $this->createContactableTalent([
            'email' => 'viewer@example.com',
        ]);

        $this->actingAs($talent)
            ->getJson(route('inbox.talent-suggestions', ['q' => 'Amine']))
            ->assertForbidden();
    }

    public function test_company_can_compose_from_inbox_after_selecting_talent(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $response = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission React',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('conversation.counterpart.id', $talent->id);
        $this->assertDatabaseHas('conversations', [
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'subject' => 'Mission React',
        ]);
    }

    public function test_company_can_open_separate_threads_for_same_talent(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission React',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ])->assertCreated();

        $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Audit Laravel',
            'body' => 'Bonjour, nous aimerions discuter d’un audit technique sur notre application.',
        ])->assertCreated();

        $this->assertDatabaseCount('conversations', 2);
        $this->assertDatabaseHas('conversations', [
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'subject' => 'Mission React',
        ]);
        $this->assertDatabaseHas('conversations', [
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'subject' => 'Audit Laravel',
        ]);
    }

    public function test_inbox_page_shows_compose_button_for_company(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);

        $this->actingAs($company)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee(__('talenma.inbox.compose_open_aria'), false);
    }

    public function test_company_can_hide_conversation_from_inbox(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $store = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission React',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $store->assertCreated();
        $conversationId = $store->json('conversation.id');

        $this->actingAs($company)
            ->deleteJson(route('inbox.destroy', $conversationId))
            ->assertOk()
            ->assertJsonPath('message', __('talenma.inbox.delete_success'));

        $this->assertNotNull(
            \App\Models\Conversation::query()->find($conversationId)?->company_hidden_at
        );

        $this->actingAs($company)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertDontSee('Mission React', false);

        $this->actingAs($talent)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee('Mission React', false);
    }

    public function test_talent_can_hide_conversation_from_inbox(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $store = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission Vue',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $store->assertCreated();
        $conversationId = $store->json('conversation.id');

        $this->actingAs($talent)
            ->deleteJson(route('inbox.destroy', $conversationId))
            ->assertOk()
            ->assertJsonPath('message', __('talenma.inbox.delete_success'));

        $this->assertNotNull(
            \App\Models\Conversation::query()->find($conversationId)?->talent_hidden_at
        );

        $this->actingAs($talent)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertDontSee('Mission Vue', false);

        $this->actingAs($company)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee('Mission Vue', false);
    }

    public function test_company_reply_revives_thread_hidden_by_talent(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $store = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission Revive',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $store->assertCreated();
        $conversationId = $store->json('conversation.id');

        $this->actingAs($talent)
            ->deleteJson(route('inbox.destroy', $conversationId))
            ->assertOk();

        $this->actingAs($talent)
            ->get(route('inbox.show', $conversationId))
            ->assertNotFound();

        $this->actingAs($company)->postJson(route('inbox.messages.store', $conversationId), [
            'body' => 'Bonjour, nous revenons vers vous avec plus de détails sur la mission.',
        ])->assertCreated();

        $this->assertNull(
            \App\Models\Conversation::query()->find($conversationId)?->talent_hidden_at
        );

        $this->actingAs($talent)
            ->get(route('inbox.show', $conversationId))
            ->assertOk()
            ->assertSee('Mission Revive', false);

        $this->actingAs($talent)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee('Mission Revive', false);
    }

    public function test_talent_reply_revives_thread_hidden_by_company(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $store = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission Company Hide',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $store->assertCreated();
        $conversationId = $store->json('conversation.id');

        $this->actingAs($company)
            ->deleteJson(route('inbox.destroy', $conversationId))
            ->assertOk();

        $this->actingAs($company)
            ->get(route('inbox.show', $conversationId))
            ->assertNotFound();

        $this->actingAs($talent)->postJson(route('inbox.messages.store', $conversationId), [
            'body' => 'Bonjour, je suis disponible pour en discuter cette semaine.',
        ])->assertCreated();

        $this->assertNull(
            \App\Models\Conversation::query()->find($conversationId)?->company_hidden_at
        );

        $this->actingAs($company)
            ->get(route('inbox.show', $conversationId))
            ->assertOk()
            ->assertSee('Mission Company Hide', false);
    }

    public function test_stale_hide_is_revived_when_counterpart_already_replied(): void
    {
        $company = User::factory()->companyOwner()->create();
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
        ]);
        $talent = $this->createContactableTalent();

        $store = $this->actingAs($company)->postJson(route('inbox.store'), [
            'talent_id' => $talent->id,
            'subject' => 'Mission Stale',
            'body' => 'Bonjour, nous cherchons un développeur pour une mission de plusieurs mois.',
        ]);

        $store->assertCreated();
        $conversationId = (int) $store->json('conversation.id');

        $this->actingAs($talent)
            ->deleteJson(route('inbox.destroy', $conversationId))
            ->assertOk();

        // Simule une réponse entreprise avant le correctif « revive » :
        // last_message_at avancé, talent_hidden_at toujours renseigné.
        $conversation = \App\Models\Conversation::query()->findOrFail($conversationId);
        $hiddenAt = now()->subMinute();
        $conversation->forceFill([
            'talent_hidden_at' => $hiddenAt,
            'last_message_at' => now(),
        ])->save();

        $this->actingAs($talent)
            ->get(route('inbox.show', $conversationId))
            ->assertOk()
            ->assertSee('Mission Stale', false);

        $this->assertNull(
            \App\Models\Conversation::query()->find($conversationId)?->talent_hidden_at
        );
    }
}
