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
    public function approved_talent_opt_in_adds_account_email_to_list(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->patch(route('newsletter.preferences'), ['newsletter_opt_in' => '1'])
            ->assertRedirect();

        $this->assertTrue($talent->fresh()->wantsNewsletter());
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => strtolower($talent->email),
            'source' => NewsletterSubscriber::SOURCE_ACCOUNT,
        ]);

        $this->actingAs($talent)
            ->patch(route('newsletter.preferences'), ['newsletter_opt_in' => '0'])
            ->assertRedirect();

        $this->assertFalse($talent->fresh()->wantsNewsletter());
        $this->assertNotNull(
            NewsletterSubscriber::query()->where('email', strtolower($talent->email))->value('unsubscribed_at')
        );
    }

    #[Test]
    public function unsubscribe_link_opts_out_subscriber(): void
    {
        $subscriber = app(NewsletterSubscriberService::class)->subscribe('bye@example.com');

        $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token))
            ->assertOk()
            ->assertSee(__('talenma.newsletter.unsubscribe_success'), false);

        $this->assertFalse($subscriber->fresh()->isActive());
    }

    #[Test]
    public function send_now_mails_only_active_open_list_subscribers(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $service = app(NewsletterSubscriberService::class);

        $active = $service->subscribe('actif@example.com');
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

        Mail::assertSent(NewsletterCampaignMail::class, function (NewsletterCampaignMail $mail) use ($active) {
            return $mail->hasTo($active->email);
        });

        Mail::assertNotSent(NewsletterCampaignMail::class, function (NewsletterCampaignMail $mail) use ($inactive) {
            return $mail->hasTo($inactive->email);
        });

        $this->assertSame(Newsletter::STATUS_SENT, $newsletter->fresh()->status);
        $this->assertSame(1, $newsletter->fresh()->recipient_count);
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
