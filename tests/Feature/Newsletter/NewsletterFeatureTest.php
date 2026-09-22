<?php

namespace Tests\Feature\Newsletter;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterCampaignMail;
use App\Models\JobPosting;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\ModeratorPermissionCatalog;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Services\ModeratorAssignmentService;
use App\Services\NewsletterSubscriberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
    public function admin_can_reorder_newsletter_blocks_on_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $newsletter = Newsletter::query()->create([
            'title' => 'Brouillon',
            'subject' => 'Sujet',
            'locale' => 'fr',
            'status' => Newsletter::STATUS_DRAFT,
            'body_blocks' => [
                ['type' => 'header', 'title' => 'A', 'subtitle' => ''],
                ['type' => 'text', 'body' => 'B'],
                ['type' => 'cta', 'label' => 'C', 'url' => 'https://example.com'],
            ],
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.newsletter.update', $newsletter), [
                'title' => 'Brouillon',
                'subject' => 'Sujet',
                'locale' => 'fr',
                'body_blocks' => json_encode([
                    ['_uid' => 'client-1', 'type' => 'text', 'body' => 'B'],
                    ['_uid' => 'client-2', 'type' => 'cta', 'label' => 'C', 'url' => 'https://example.com'],
                    ['_uid' => 'client-3', 'type' => 'header', 'title' => 'A', 'subtitle' => ''],
                ]),
            ])
            ->assertRedirect();

        $blocks = $newsletter->fresh()->normalizedBlocks();
        $this->assertSame(['text', 'cta', 'header'], array_column($blocks, 'type'));
        $this->assertArrayNotHasKey('_uid', $blocks[0]);
    }

    #[Test]
    public function free_text_block_renders_as_distinct_card(): void
    {
        $html = app(\App\Services\NewsletterRenderer::class)->renderBlock([
            'type' => Newsletter::BLOCK_TEXT,
            'body' => "Message plateforme\nLigne 2",
        ], 'fr');

        $this->assertStringContainsString('border:1px solid #e5e7eb', $html);
        $this->assertStringContainsString('border-radius:12px', $html);
        $this->assertStringContainsString('background:#f9fafb', $html);
        $this->assertStringContainsString('Message plateforme', $html);
        $this->assertStringContainsString('Ligne 2', $html);
    }

    #[Test]
    public function newsletter_email_uses_dated_title(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-17'));

        $html = app(\App\Services\NewsletterRenderer::class)->renderHtml(new Newsletter([
            'locale' => 'fr',
            'body_blocks' => [
                ['type' => 'text', 'body' => 'Contenu'],
            ],
        ]));

        $this->assertStringContainsString('Newsletter du Jeudi 17 Septembre 2026', $html);
        $this->assertStringContainsString('max-width:728px', $html);
        $this->assertStringNotContainsString('Newsletter TDM', $html);
        $this->assertDoesNotMatchRegularExpression('/<p[^>]*>\s*Talents du Maroc\s*<\/p>/', $html);

        Carbon::setTestNow();
    }

    #[Test]
    public function newsletter_email_includes_fixed_site_footer(): void
    {
        $html = app(\App\Services\NewsletterRenderer::class)->renderHtml(new Newsletter([
            'locale' => 'fr',
            'body_blocks' => [
                ['type' => 'text', 'body' => 'Contenu'],
            ],
        ]));

        $this->assertStringContainsString('background:#111827', $html);
        $this->assertStringContainsString('text-align:center', $html);
        $this->assertStringContainsString('www.talentsdumaroc.com', $html);
        $this->assertStringContainsString('https://www.talentsdumaroc.com', $html);
        $this->assertStringContainsString('images/logo2-white.png', $html);
        $this->assertStringContainsString('Nous suivre', $html);
        $this->assertStringContainsString((string) config('talenma.social.linkedin'), $html);
        $this->assertStringContainsString((string) config('talenma.social.facebook'), $html);
        $this->assertStringContainsString((string) config('talenma.social.instagram'), $html);
        $this->assertStringNotContainsString(__('talenma.footer.tagline'), $html);
        $this->assertStringNotContainsString('Visiter le site', $html);
        $this->assertStringNotContainsString('Créer un compte', $html);
        $this->assertStringNotContainsString(__('talenma.footer.privacy'), $html);
        $this->assertStringNotContainsString((string) config('talenma.social.x'), $html);
        $this->assertStringNotContainsString((string) config('talenma.social.youtube'), $html);
        $this->assertStringNotContainsString('KHALID JADI', $html);
        $this->assertStringNotContainsString('JADI DIGITAL', $html);
    }

    #[Test]
    public function jobs_block_heading_renders_as_section_separator(): void
    {
        $html = app(\App\Services\NewsletterRenderer::class)->renderBlock([
            'type' => Newsletter::BLOCK_JOBS,
            'heading' => 'Annonces à la une',
            'job_ids' => [999999],
        ], 'fr');

        $this->assertSame('', $html);

        $jobHtmlHeading = (new \ReflectionClass(\App\Services\NewsletterRenderer::class))
            ->getMethod('sectionHeading');
        $jobHtmlHeading->setAccessible(true);
        $headingHtml = $jobHtmlHeading->invoke(app(\App\Services\NewsletterRenderer::class), 'Annonces à la une');

        $this->assertStringContainsString('Annonces à la une', $headingHtml);
        $this->assertStringContainsString('background:#eef2ff', $headingHtml);
        $this->assertStringContainsString('color:#3730a3', $headingHtml);
    }

    #[Test]
    public function jobs_block_floats_advertiser_thumbnail_top_right(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $job = JobPosting::query()->create([
            'company_profile_id' => null,
            'created_by' => $admin->id,
            'title' => 'Ingénieur backend',
            'description' => str_repeat('Poste ouvert pour un profil backend confirmé. ', 3),
            'status' => JobPosting::STATUS_PUBLISHED,
            'published_at' => now(),
            'application_mode' => JobPosting::APPLICATION_EXTERNAL,
            'external_company_name' => 'Acme Partners',
            'external_company_logo_path' => 'https://cdn.example.com/logos/acme.png',
            'external_apply_url' => 'https://acme.example/jobs/apply',
        ]);

        $html = app(\App\Services\NewsletterRenderer::class)->renderBlock([
            'type' => Newsletter::BLOCK_JOBS,
            'heading' => 'Annonces à la une',
            'job_ids' => [$job->id],
        ], 'fr');

        $this->assertStringContainsString('Ingénieur backend', $html);
        $this->assertStringContainsString('Acme Partners', $html);
        $this->assertStringContainsString('https://cdn.example.com/logos/acme.png', $html);
        $this->assertStringContainsString('float:right', $html);
        $this->assertStringContainsString('width="56"', $html);
        $this->assertStringContainsString('display:block;padding:12px 14px;text-decoration:none;color:inherit;', $html);
        $this->assertSame(1, substr_count($html, 'href="'.route('jobs.gate', $job).'"'));
    }

    #[Test]
    public function register_banner_block_renders_fixed_signup_ad(): void
    {
        $html = app(\App\Services\NewsletterRenderer::class)->renderBlock([
            'type' => Newsletter::BLOCK_REGISTER,
        ], 'fr');

        $this->assertStringContainsString('https://talentsdumaroc.com/register', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('Créer mon compte', $html);
        $this->assertStringNotContainsString('Créer mon compte sur Talents du Maroc', $html);
        $this->assertStringContainsString('Rejoignez la communauté', $html);
        $this->assertStringNotContainsString('Espace talents', $html);
        $this->assertStringNotContainsString('vitrine talent', $html);
        $this->assertStringContainsString('background:#4f46e5', $html);
        $this->assertStringContainsString('background:#fbbf24', $html);
    }

    #[Test]
    public function talent_spotlight_cards_include_name_and_photo(): void
    {
        $talent = User::factory()->talent()->create([
            'first_name' => 'Amina',
            'last_name' => 'El Fassi',
            'avatar_path' => 'avatars/amina.png',
        ]);
        $talent->profile()->create([
            'experience_years' => 0,
            'city' => 'Casablanca',
        ]);

        $html = app(\App\Services\NewsletterRenderer::class)->renderHtml(new Newsletter([
            'locale' => 'fr',
            'body_blocks' => [
                ['type' => Newsletter::BLOCK_TALENTS, 'heading' => 'Talents à découvrir', 'user_ids' => [$talent->id]],
            ],
        ]));

        $this->assertStringContainsString('Talents à découvrir', $html);
        $this->assertStringContainsString('background:#eef2ff', $html);
        $this->assertStringContainsString('Amina El Fassi', $html);
        $this->assertStringContainsString('Casablanca', $html);
        $this->assertStringContainsString('avatars/amina.png', $html);
        $this->assertStringContainsString('border-radius:50%', $html);
    }

    #[Test]
    public function create_form_exposes_library_and_cv_template_blocks(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->get(route('admin.newsletter.create'))
            ->assertOk()
            ->assertSee('Derniers ouvrages', false)
            ->assertSee('Nouveaux modèles de CV', false)
            ->assertSee('cv_templates', false);
    }

    #[Test]
    public function library_picker_lists_only_the_ten_latest_published_books(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $category = $this->libraryCategory();

        $this->libraryBook($category, 'Atlas des sols');
        $this->libraryBook($category, 'Manuel disparu');
        for ($i = 1; $i <= 10; $i++) {
            $this->libraryBook($category, 'Guide récent '.$i);
        }

        $this->actingAs($admin)
            ->get(route('admin.newsletter.create'))
            ->assertOk()
            ->assertSee('Cochez parmi les 10 derniers ouvrages mis en ligne', false)
            ->assertSee('Guide récent 1', false)
            ->assertSee('Guide récent 10', false)
            ->assertDontSee('Atlas des sols', false)
            ->assertDontSee('Manuel disparu', false);
    }

    #[Test]
    public function library_block_renders_cover_title_and_root_discipline_three_per_row(): void
    {
        $root = LibraryCategory::query()->create([
            'slug' => 'informatique',
            'name_fr' => 'Informatique',
            'name_en' => 'IT',
            'depth' => 1,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $sub = LibraryCategory::query()->create([
            'parent_id' => $root->id,
            'slug' => 'developpement',
            'name_fr' => 'Développement',
            'name_en' => 'Development',
            'depth' => 2,
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $leaf = LibraryCategory::query()->create([
            'parent_id' => $sub->id,
            'slug' => 'programmation-systeme',
            'name_fr' => 'Programmation système',
            'name_en' => 'Systems programming',
            'depth' => 3,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $bookA = $this->libraryBook($leaf, 'Introduction complète au droit international privé et à la procédure civile comparée', 'https://cdn.example.com/covers/laravel.jpg');
        $bookB = $this->libraryBook($leaf, 'Guide Redis', 'https://cdn.example.com/covers/redis.jpg');
        $bookC = $this->libraryBook($leaf, 'Guide Linux', 'https://cdn.example.com/covers/linux.jpg');
        $hidden = $this->libraryBook($leaf, 'Brouillon secret', 'https://cdn.example.com/covers/secret.jpg', published: false);

        $html = app(\App\Services\NewsletterRenderer::class)->renderHtml(new Newsletter([
            'locale' => 'fr',
            'body_blocks' => [[
                'type' => Newsletter::BLOCK_LIBRARY,
                'heading' => 'Derniers ouvrages mis en ligne',
                'book_ids' => [$bookA->id, $bookB->id, $bookC->id, $hidden->id],
            ]],
        ]));

        $this->assertStringContainsString('Derniers ouvrages mis en ligne', $html);
        $this->assertStringContainsString('background:#eef2ff', $html);
        $this->assertStringContainsString('Introduction complète au droit international', $html);
        $this->assertStringNotContainsString('procédure civile comparée', $html);
        $this->assertStringContainsString('Guide Redis', $html);
        $this->assertStringContainsString('Guide Linux', $html);
        $this->assertStringContainsString('Informatique', $html);
        $this->assertStringNotContainsString('Programmation système', $html);
        $this->assertStringNotContainsString('Développement', $html);
        $this->assertStringContainsString('https://cdn.example.com/covers/laravel.jpg', $html);
        $this->assertStringContainsString('width="96"', $html);
        $this->assertStringContainsString('height="88"', $html);
        $this->assertStringContainsString('height:176px', $html);
        $this->assertStringContainsString('max-height:28px', $html);
        $this->assertStringContainsString('width="33%"', $html);
        $this->assertStringNotContainsString('width="50%"', $html);
        $this->assertStringContainsString(route('library.gate'), $html);
        $this->assertStringNotContainsString('Brouillon secret', $html);
    }

    #[Test]
    public function library_block_ignores_books_outside_the_ten_latest(): void
    {
        $category = $this->libraryCategory();
        $oldest = $this->libraryBook($category, 'Atlas des sols');
        for ($i = 1; $i <= 10; $i++) {
            $this->libraryBook($category, 'Guide récent '.$i);
        }

        $html = app(\App\Services\NewsletterRenderer::class)->renderBlock([
            'type' => Newsletter::BLOCK_LIBRARY,
            'heading' => 'Derniers ouvrages mis en ligne',
            'book_ids' => [$oldest->id],
        ], 'fr');

        $this->assertSame('', $html);
    }

    #[Test]
    public function cv_templates_block_renders_preview_and_editable_description_per_row(): void
    {
        $html = app(\App\Services\NewsletterRenderer::class)->renderHtml(new Newsletter([
            'locale' => 'fr',
            'body_blocks' => [[
                'type' => Newsletter::BLOCK_CV_TEMPLATES,
                'heading' => 'Nouveaux modèles de CV',
                'template_keys' => ['normal', 'starter', 'not-a-template'],
                'template_descriptions' => [
                    'normal' => "Mise en page claire\npour un premier job",
                    'starter' => 'Idéal pour démarrer',
                ],
            ]],
        ]));

        $this->assertStringContainsString('Nouveaux modèles de CV', $html);
        $this->assertStringContainsString('background:#eef2ff', $html);
        $this->assertStringContainsString('Normal', $html);
        $this->assertStringContainsString('Starter', $html);
        $this->assertStringContainsString('Mise en page claire', $html);
        $this->assertStringContainsString('pour un premier job', $html);
        $this->assertStringContainsString('Idéal pour démarrer', $html);
        $this->assertStringContainsString('marketing-preview-normal-fr.png', $html);
        $this->assertStringContainsString('width="96"', $html);
        $this->assertStringContainsString('height:88px', $html);
        $this->assertStringContainsString('valign="middle"', $html);
        $this->assertStringNotContainsString('width="33%"', $html);
        $this->assertStringContainsString(route('cv-builder.gate'), $html);
        $this->assertStringNotContainsString('not-a-template', $html);
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

        // First email is sent immediately; the rest wait for the 1/3 minutes processor.
        Mail::assertSentCount(1);
        $this->assertSame(Newsletter::STATUS_SENDING, $newsletter->fresh()->status);
        $this->assertSame(2, \App\Models\NewsletterOutbox::query()->where('newsletter_id', $newsletter->id)->count());
        $this->assertSame(1, \App\Models\NewsletterOutbox::query()->where('status', 'pending')->count());

        app(\App\Services\NewsletterDeliveryService::class)->processNextPending();

        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($talent->email));
        Mail::assertSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($open->email));
        Mail::assertNotSent(NewsletterCampaignMail::class, fn (NewsletterCampaignMail $mail) => $mail->hasTo($inactive->email));

        $this->assertSame(Newsletter::STATUS_SENT, $newsletter->fresh()->status);
        $this->assertSame(2, $newsletter->fresh()->recipient_count);
    }

    #[Test]
    public function admin_can_send_test_email_without_starting_campaign(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'approval_status' => null,
            'email' => 'admin@example.com',
        ]);

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
            ->post(route('admin.newsletter.send-test', $newsletter), [
                'email' => 'admin@example.com',
            ])
            ->assertRedirect(route('admin.newsletter.show', $newsletter));

        Mail::assertSent(NewsletterCampaignMail::class, function (NewsletterCampaignMail $mail) {
            return $mail->hasTo('admin@example.com')
                && $mail->isTest === true
                && $mail->envelope()->subject === '[TEST] Sujet test';
        });

        $this->assertSame(Newsletter::STATUS_DRAFT, $newsletter->fresh()->status);
        $this->assertSame(0, \App\Models\NewsletterOutbox::query()->count());
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

    private function libraryCategory(): LibraryCategory
    {
        return LibraryCategory::query()->create([
            'slug' => 'droit',
            'name_fr' => 'Droit',
            'name_en' => 'Law',
            'depth' => 3,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function libraryBook(
        LibraryCategory $category,
        string $title,
        ?string $cover = null,
        bool $published = true,
    ): LibraryBook {
        $slug = str_replace(' ', '-', strtolower($title));

        return LibraryBook::query()->create([
            'library_category_id' => $category->id,
            'title' => $title,
            'author' => 'Auteur',
            'cover_path' => $cover,
            'file_path' => 'library-books/'.$slug.'.pdf',
            'original_filename' => $slug.'.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 12,
            'is_published' => $published,
        ]);
    }
}
