<?php

namespace Tests\Feature\Newsletter;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterCampaignMail;
use App\Models\ModeratorPermissionCatalog;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use App\Services\NewsletterSubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewsletterFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_create_draft_newsletter(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->post(route('admin.newsletter.store'), [
                'title' => 'Semaine du 8 sept',
                'subject' => 'Les opportunités de la semaine',
                'locale' => 'fr',
                'body_blocks' => json_encode([
                    ['type' => 'header', 'title' => 'Bonjour', 'subtitle' => 'Chapô'],
                    ['type' => 'text', 'body' => "Message plateforme\nLigne 2"],
                ]),
            ])
            ->assertRedirect();

        $newsletter = Newsletter::query()->first();
        $this->assertNotNull($newsletter);
        $this->assertSame(Newsletter::STATUS_DRAFT, $newsletter->status);
        $this->assertSame('Semaine du 8 sept', $newsletter->title);
        $this->assertCount(2, $newsletter->normalizedBlocks());
    }

    #[Test]
    public function moderator_without_permission_cannot_access_newsletter(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::PUBLICATIONS_MANAGE,
        ])->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.newsletter.index'))
            ->assertForbidden();
    }

    #[Test]
    public function moderator_with_newsletter_permission_can_access(): void
    {
        $moderator = User::factory()->moderator([
            ModeratorPermissionCatalog::NEWSLETTER_MANAGE,
        ])->create();

        $this->withSession([ModeratorAssignmentService::SESSION_MODE_KEY => true])
            ->actingAs($moderator)
            ->get(route('admin.newsletter.index'))
            ->assertOk();
    }

    #[Test]
    public function guest_can_subscribe_by_email_without_account(): void
    {
        $this->from(route('home'))
            ->post(route('newsletter.subscribe'), ['email' => 'lecteur@example.com'])
            ->assertRedirect(route('home'));

        $subscriber = NewsletterSubscriber::query()->where('email', 'lecteur@example.com')->first();
        $this->assertNotNull($subscriber);
        $this->assertTrue($subscriber->isActive());
        $this->assertSame(NewsletterSubscriber::SOURCE_PUBLIC, $subscriber->source);
    }

    #[Test]
    public function admin_can_add_emails_to_open_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->post(route('admin.newsletter.subscribers.store'), [
                'emails' => "a@example.com\nb@example.com, c@example.com",
            ])
            ->assertRedirect();

        $this->assertSame(3, NewsletterSubscriber::query()->active()->count());
    }

    #[Test]
    public function profile_newsletter_preference_route_is_removed(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->patch('/newsletter/preferences', ['newsletter_opt_in' => '1'])
            ->assertNotFound();
    }

    #[Test]
    public function unsubscribe_link_opts_out_open_list_subscriber(): void
    {
        $subscriber = app(NewsletterSubscriberService::class)->subscribe('bye@example.com');

        $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token))
            ->assertOk()
            ->assertSee(__('talenma.newsletter.unsubscribe_success'), false);

        $this->assertFalse($subscriber->fresh()->isActive());
    }

    #[Test]
    public function send_now_includes_approved_talents_and_open_list(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $service = app(NewsletterSubscriberService::class);

        $talent = User::factory()->talent()->create(['email' => 'talent@example.com']);
        $open = $service->subscribe('ouvert@example.com');
        $inactive = $service->subscribe('inactif@example.com');
        $service->unsubscribe($inactive);

        $newsletter = Newsletter::query()->create([
            'title' => 'Campagne',
            'subject' => 'Sujet test',
            'locale' => 'fr',
            'status' => Newsletter::STATUS_DRAFT,
            'body_blocks' => [
                ['type' => 'header', 'title' => 'Hello', 'subtitle' => ''],
            ],
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.newsletter.send', $newsletter))
            ->assertRedirect(route('admin.newsletter.show', $newsletter));

        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($talent->email));
        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($open->email));
        Mail::assertNotSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($inactive->email));

        $this->assertSame(Newsletter::STATUS_SENT, $newsletter->fresh()->status);
        $this->assertSame(2, $newsletter->fresh()->recipient_count);
    }

    #[Test]
    public function schedule_dispatches_delayed_job(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $newsletter = Newsletter::query()->create([
            'title' => 'Programmée',
            'subject' => 'Plus tard',
            'locale' => 'fr',
            'status' => Newsletter::STATUS_DRAFT,
            'body_blocks' => [
                ['type' => 'text', 'body' => 'Contenu'],
            ],
            'created_by' => $admin->id,
        ]);

        $when = now()->addDay()->format('Y-m-d\TH:i');

        $this->actingAs($admin)
            ->post(route('admin.newsletter.schedule', $newsletter), [
                'scheduled_at' => $when,
            ])
            ->assertRedirect(route('admin.newsletter.show', $newsletter));

        $this->assertSame(Newsletter::STATUS_SCHEDULED, $newsletter->fresh()->status);

        Queue::assertPushed(SendNewsletterJob::class, function (SendNewsletterJob $job) use ($newsletter) {
            return $job->newsletterId === $newsletter->id;
        });
    }
}
