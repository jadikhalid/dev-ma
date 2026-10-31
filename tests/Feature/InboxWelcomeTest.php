<?php

namespace Tests\Feature;

use App\Mail\InboxWelcomeMail;
use App\Mail\NewInboxMessageMail;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InboxWelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_receives_staff_welcome_thread_without_email_on_first_inbox_visit(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'approval_status' => null,
            'email' => 'admin-welcome@example.com',
        ]);

        $company = User::factory()->companyOwner()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'inbox_welcome_sent_at' => null,
        ]);
        $company->companyProfile()->create([
            'country' => 'fr',
            'is_subscribed' => true,
            'representative_name' => 'Samir Contact',
        ]);

        $this->actingAs($company)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee(__('talenma.inbox.welcome_subject'), false)
            ->assertSee(__('talenma.inbox.staff_counterpart'), false);

        $company->refresh();
        $this->assertNotNull($company->inbox_welcome_sent_at);

        $this->assertDatabaseHas('conversations', [
            'company_user_id' => $company->id,
            'channel' => Conversation::CHANNEL_STAFF,
            'subject' => __('talenma.inbox.welcome_subject'),
        ]);

        Mail::assertNotSent(InboxWelcomeMail::class);
        Mail::assertNotSent(NewInboxMessageMail::class);
    }

    public function test_talent_receives_platform_welcome_thread_without_email_on_first_inbox_visit(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'approval_status' => null,
            'email' => 'admin-talent-welcome@example.com',
        ]);

        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'inbox_welcome_sent_at' => null,
        ]);
        $talent->profile()->create([
            'bio' => 'Profil test',
            'experience_years' => 2,
        ]);

        $this->actingAs($talent)
            ->get(route('inbox.index'))
            ->assertOk()
            ->assertSee(__('talenma.inbox.welcome_subject'), false)
            ->assertSee(__('talenma.inbox.staff_counterpart'), false);

        $talent->refresh();
        $this->assertNotNull($talent->inbox_welcome_sent_at);

        $this->assertDatabaseHas('conversations', [
            'company_user_id' => $admin->id,
            'talent_user_id' => $talent->id,
            'channel' => Conversation::CHANNEL_TALENT,
            'subject' => __('talenma.inbox.welcome_subject'),
        ]);

        $this->assertSame(1, app(\App\Services\MessagingService::class)->unreadCountFor($talent->fresh()));

        Mail::assertNotSent(InboxWelcomeMail::class);
        Mail::assertNotSent(NewInboxMessageMail::class);
    }
}
