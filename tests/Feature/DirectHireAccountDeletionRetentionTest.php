<?php

namespace Tests\Feature;

use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\User;
use App\Services\UserDeletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectHireAccountDeletionRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_deletion_keeps_direct_hire_for_company(): void
    {
        [$company, $talent, $hire] = $this->makeOpenDirectHire();
        $hireId = $hire->id;

        app(UserDeletionService::class)->delete($talent);

        $this->assertNull($talent->fresh());
        $this->assertDatabaseHas('direct_hire_requests', [
            'id' => $hireId,
            'company_user_id' => $company->id,
            'talent_user_id' => null,
            'talent_name_snapshot' => 'Talent Alpha',
            'status' => DirectHireRequest::STATUS_WITHDRAWN,
        ]);
        $this->assertDatabaseHas('direct_hire_messages', [
            'direct_hire_request_id' => $hireId,
            'sender_user_id' => $company->id,
        ]);

        $retained = DirectHireRequest::query()->findOrFail($hireId);
        $this->assertSame('Talent Alpha', $retained->talentDisplayName());
    }

    public function test_company_deletion_keeps_direct_hire_for_talent(): void
    {
        [$company, $talent, $hire] = $this->makeOpenDirectHire();
        $hireId = $hire->id;

        app(UserDeletionService::class)->delete($company);

        $this->assertNull($company->fresh());
        $this->assertDatabaseHas('direct_hire_requests', [
            'id' => $hireId,
            'talent_user_id' => $talent->id,
            'company_user_id' => null,
            'company_profile_id' => null,
            'company_name_snapshot' => 'Acme Corp',
            'status' => DirectHireRequest::STATUS_WITHDRAWN,
        ]);

        $retained = DirectHireRequest::query()->findOrFail($hireId);
        $this->assertSame('Acme Corp', $retained->companyDisplayName());
    }

    public function test_direct_hire_is_purged_only_when_both_parties_are_deleted(): void
    {
        [$company, $talent, $hire] = $this->makeOpenDirectHire();
        $hireId = $hire->id;

        app(UserDeletionService::class)->delete($talent);
        $this->assertDatabaseHas('direct_hire_requests', ['id' => $hireId]);

        app(UserDeletionService::class)->delete($company);
        $this->assertDatabaseMissing('direct_hire_requests', ['id' => $hireId]);
        $this->assertDatabaseMissing('direct_hire_messages', ['direct_hire_request_id' => $hireId]);
    }

    public function test_chat_messages_survive_when_sender_account_is_deleted(): void
    {
        [$company, $talent, $hire] = $this->makeOpenDirectHire();

        $message = DirectHireMessage::query()->create([
            'direct_hire_request_id' => $hire->id,
            'sender_user_id' => $talent->id,
            'body' => 'Bonjour de mon côté',
        ]);

        app(UserDeletionService::class)->delete($talent);

        $this->assertDatabaseHas('direct_hire_messages', [
            'id' => $message->id,
            'sender_user_id' => null,
            'body' => 'Bonjour de mon côté',
        ]);
    }

    /**
     * @return array{0: User, 1: User, 2: DirectHireRequest}
     */
    private function makeOpenDirectHire(): array
    {
        $company = User::factory()->companyOwner()->create([
            'name' => 'Acme Corp',
        ]);
        $company->companyProfile()->create(['country' => 'fr']);

        $talent = User::factory()->talent()->create([
            'name' => 'Talent Alpha',
        ]);

        $hire = DirectHireRequest::query()->create([
            'company_user_id' => $company->id,
            'talent_user_id' => $talent->id,
            'talent_name_snapshot' => $talent->name,
            'company_profile_id' => $company->companyProfile->id,
            'company_name_snapshot' => $company->name,
            'subject' => 'Software engineer',
            'message' => str_repeat('Proposition détaillée. ', 4),
            'status' => DirectHireRequest::STATUS_PENDING_RESPONSE,
            'company_seen_at' => now(),
        ]);

        DirectHireMessage::query()->create([
            'direct_hire_request_id' => $hire->id,
            'sender_user_id' => $company->id,
            'body' => $hire->message,
        ]);

        return [$company, $talent, $hire];
    }
}
