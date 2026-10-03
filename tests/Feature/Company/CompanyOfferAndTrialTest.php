<?php

namespace Tests\Feature\Company;

use App\Mail\CompanyApprovedMail;
use App\Mail\CompanyDemoConfirmationMail;
use App\Mail\CompanyDemoRequestMail;
use App\Mail\CompanyTrialRequestMail;
use App\Models\CompanyDemoRequest;
use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Models\User;
use App\Services\UserModerationService;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompanyOfferAndTrialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProfessionSeeder::class);
    }

    public function test_company_offer_page_is_public(): void
    {
        $response = $this->get(route('company.offer'));

        $response
            ->assertOk()
            ->assertSee(__('talenma.company_offer.cta_demo'), false)
            ->assertSee(__('talenma.company_offer.hero_cta_trial'), false)
            ->assertSee(__('talenma.company_offer.trial_submit'), false)
            ->assertDontSee(__('talenma.nav.jobs'), false)
            ->assertDontSee(__('talenma.nav.blog'), false)
            ->assertDontSee(__('talenma.nav.apps_launcher_title'), false)
            ->assertSee('name="first_name"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('data-trial-wizard', false)
            ->assertDontSee('name="role"', false)
            ->assertSee(route('company.trial.store'), false);

        $this->assertSame(1, substr_count($response->getContent(), 'name="password"'));
        $response->assertSee('id="company-login-password"', false);

        $response->assertSeeText(__('talenma.company_offer.includes_1'));
        $response->assertSeeText(__('talenma.company_offer.includes_2'));
        $response->assertSeeText(__('talenma.company_offer.includes_3'));
        $response->assertSeeText(__('talenma.company_offer.includes_4'));
    }

    public function test_company_offer_hero_renders_closed_form_drawers(): void
    {
        User::factory()->count(2)->create(['role' => 'dev', 'approval_status' => User::APPROVAL_APPROVED]);
        User::factory()->create(['role' => 'dev', 'approval_status' => User::APPROVAL_PENDING]);

        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSeeText(__('talenma.company_offer.hero_title'))
            ->assertSee('drawer: null', false)
            ->assertSee('data-company-offer-drawer="demo"', false)
            ->assertSee('data-company-offer-drawer="trial"', false)
            ->assertSee('<p class="text-xl font-extrabold text-gray-950 sm:text-3xl" data-company-offer-talent-count>2+</p>', false);
    }

    public function test_company_offer_hero_shows_approved_company_count(): void
    {
        foreach ([User::APPROVAL_APPROVED, User::APPROVAL_APPROVED, User::APPROVAL_APPROVED, User::APPROVAL_PENDING] as $status) {
            $company = User::factory()->companyOwner()->create(['approval_status' => $status]);
            CompanyProfile::factory()->create(['user_id' => $company->id]);
        }

        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-offer-company-count>3+</p>', false);
    }

    public function test_company_offer_footer_has_no_newsletter_and_agency_credit_only(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('<footer', false)
            ->assertSee(route('privacy'), false)
            ->assertSee('https://www.jadi-digital.com/', false)
            ->assertDontSee(route('newsletter.subscribe'), false)
            ->assertDontSee(__('talenma.footer.developer_name'), false);
    }

    public function test_case_studies_page_renders_company_header_band_and_footer(): void
    {
        $this->get(route('company.offer'))
            ->assertSee(route('company.case-studies'), false)
            ->assertSee(__('talenma.nav.case_studies'));

        $this->get(route('company.case-studies'))
            ->assertOk()
            ->assertSee('data-company-header-band', false)
            ->assertSee('data-company-case-studies', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('https://www.jadi-digital.com/', false)
            ->assertDontSee(route('newsletter.subscribe'), false);
    }

    public function test_offers_and_about_pages_render_with_band_links(): void
    {
        $this->get(route('company.offer'))
            ->assertSee(route('company.offers'), false)
            ->assertSee(route('company.about'), false)
            ->assertSee(__('talenma.nav.offers'))
            ->assertSee(__('talenma.nav.about'), false);

        foreach (['company.offers' => 'data-company-offers', 'company.about' => 'data-company-about'] as $route => $marker) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('data-company-header-band', false)
                ->assertSee($marker, false)
                ->assertSee('aria-current="page"', false)
                ->assertSee('https://www.jadi-digital.com/', false);
        }
    }

    public function test_mobile_nav_drawer_lists_band_links_and_offer_ctas(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-mobile-nav-toggle', false)
            ->assertSee('data-company-mobile-home', false)
            ->assertSee('data-company-mobile-nav', false)
            ->assertSee('data-company-mobile-platform-toggle', false)
            ->assertSee('data-company-mobile-demo', false)
            ->assertSee('data-company-mobile-trial', false);
    }

    public function test_company_offer_header_platform_menu_lists_the_four_services(): void
    {
        $response = $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-platform-trigger', false)
            ->assertSee('data-company-platform-menu', false)
            ->assertSee(__('talenma.nav.platform_menu_title'));

        foreach (range(1, 4) as $index) {
            $response->assertSee(__('talenma.company_offer.includes_'.$index));
        }
    }

    public function test_company_offer_shows_a_showcase_section_for_each_platform_service(): void
    {
        $response = $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-platform-intro', false)
            ->assertSee(__('talenma.company_offer.platform_title'))
            ->assertSee('data-company-platform-final', false);

        foreach (['catalogue', 'applications', 'sourcing', 'jobs'] as $key) {
            $response
                ->assertSee('data-company-platform-service="'.$key.'"', false)
                ->assertSee('id="platform-'.$key.'"', false)
                ->assertSee('data-company-platform-link="platform-'.$key.'"', false)
                ->assertSee(__('talenma.company_offer.services.'.$key.'.title'));
        }
    }

    public function test_logged_in_company_home_redirects_to_dashboard(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->get(route('company.offer', ['tab' => 'trial']))
            ->assertRedirect(route('dashboard'));
    }

    public function test_logged_in_company_header_has_no_home_link_and_logo_targets_dashboard(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('data-nav-home-link', false)
            ->assertDontSee('href="'.route('company.offer').'"', false);
    }

    public function test_company_offer_trial_tab_opens_trial_drawer(): void
    {
        $this->get(route('company.offer', ['tab' => 'trial']))
            ->assertOk()
            ->assertSee('drawer: \'trial\'', false);
    }

    public function test_register_company_query_redirects_to_offer(): void
    {
        $this->get(route('register', ['role' => 'company']))
            ->assertRedirect(route('company.offer', ['tab' => 'trial']));
    }

    public function test_register_page_is_talent_only(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee(__('talenma.auth.register_title_talent'), false)
            ->assertDontSee(__('talenma.auth.register_as'), false)
            ->assertSee('value="dev"', false);
    }

    public function test_company_cannot_self_register_via_register_endpoint(): void
    {
        Mail::fake();

        $this->from(route('company.offer', ['tab' => 'trial']))
            ->post('/register', [
                'name' => 'Acme SAS',
                'email' => 'company@example.com',
                'role' => 'company',
                'contact_name' => 'Jean Dupont',
                'phone' => '+33123456789',
                'sector' => 'it-digital',
                'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
                'company_country' => 'fr',
                'data_processing_consent' => '1',
            ])
            ->assertRedirect(route('company.offer', ['tab' => 'trial']))
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);
        $this->assertDatabaseMissing('company_trial_requests', ['email' => 'company@example.com']);
        Mail::assertNothingSent();
    }

    public function test_demo_request_is_stored_and_emailed(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->post(route('company.demo.store'), $this->demoPayload());

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $this->assertDatabaseHas('company_demo_requests', [
            'company_name' => 'Acme SAS',
            'email' => 'jean@acme.test',
        ]);

        Mail::assertSent(CompanyDemoRequestMail::class, function (CompanyDemoRequestMail $mail) use ($admin) {
            return $mail->hasTo($admin->email)
                && $mail->demoRequest->company_name === 'Acme SAS';
        });

        $this->assertSame(1, CompanyDemoRequest::query()->count());

        Mail::assertSent(CompanyDemoConfirmationMail::class, function (CompanyDemoConfirmationMail $mail) {
            return $mail->hasTo('jean@acme.test')
                && $mail->demoRequest->company_name === 'Acme SAS';
        });
        $this->assertSame(CompanyDemoRequest::STATUS_NEW, CompanyDemoRequest::query()->first()->status);
    }

    public function test_demo_confirmation_mail_mentions_company_and_message(): void
    {
        $demo = $this->makeDemoRequest();

        $html = (new CompanyDemoConfirmationMail($demo))->render();

        $this->assertStringContainsString('Acme SAS', $html);
        $this->assertStringContainsString('Jean Dupont', $html);
        $this->assertStringContainsString('Nous voulons voir la plateforme.', $html);
    }

    public function test_admin_can_list_demo_requests_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeDemoRequest(['company_name' => 'Nouvelle SARL']);
        $this->makeDemoRequest(['company_name' => 'Planifiee SA', 'status' => CompanyDemoRequest::STATUS_SCHEDULED]);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('Nouvelle SARL')
            ->assertDontSee('Planifiee SA')
            ->assertSee('data-demo-new-count', false);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index', ['status' => CompanyDemoRequest::STATUS_SCHEDULED]))
            ->assertOk()
            ->assertSee('Planifiee SA')
            ->assertDontSee('Nouvelle SARL');
    }

    public function test_admin_can_update_demo_request_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $demo = $this->makeDemoRequest();

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_SCHEDULED])
            ->assertRedirect()
            ->assertSessionHas('toast_success');

        $demo->refresh();
        $this->assertSame(CompanyDemoRequest::STATUS_SCHEDULED, $demo->status);
        $this->assertSame($admin->id, $demo->handled_by);
        $this->assertNotNull($demo->handled_at);

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_NEW]);

        $demo->refresh();
        $this->assertSame(CompanyDemoRequest::STATUS_NEW, $demo->status);
        $this->assertNull($demo->handled_by);
        $this->assertNull($demo->handled_at);

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => 'bogus'])
            ->assertSessionHasErrors('status');
    }

    public function test_non_staff_cannot_access_demo_requests(): void
    {
        $company = User::factory()->create(['role' => 'company']);
        $demo = $this->makeDemoRequest();

        $this->actingAs($company)
            ->get(route('admin.company-demo-requests.index'))
            ->assertForbidden();

        $this->actingAs($company)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_DONE])
            ->assertForbidden();

        $this->assertSame(CompanyDemoRequest::STATUS_NEW, $demo->fresh()->status);
    }

    public function test_admin_header_links_sit_in_an_overflow_row_with_more_menu(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('x-data="navOverflow"', false)
            ->assertSee('data-nav-overflow-row', false)
            ->assertSee('data-nav-more-button', false)
            ->assertSee('aria-label="'.__('talenma.nav.more').'"', false)
            ->assertSee(__('talenma.nav.admin_company_demos'));
    }

    public function test_demo_request_stores_qualification_answers_and_combines_phone(): void
    {
        Mail::fake();
        User::factory()->create(['role' => 'admin']);

        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'phone_country' => 'ma',
            'phone' => '0622119177',
            'hiring_locations' => ['ma', 'fr', 'ma'],
            'message' => '',
        ]))->assertOk();

        $demo = CompanyDemoRequest::query()->firstOrFail();
        $this->assertSame('Jean', $demo->first_name);
        $this->assertSame('Dupont', $demo->last_name);
        $this->assertSame('Jean Dupont', $demo->contact_name);
        $this->assertSame('+212 622119177', $demo->phone);
        $this->assertSame('11-50', $demo->company_size);
        $this->assertSame('1-4', $demo->hires_planned);
        $this->assertSame(['ma', 'fr'], $demo->hiring_locations);
        $this->assertSame('Casablanca', $demo->hiring_city);
        $this->assertSame('no', $demo->uses_ats);
        $this->assertNull($demo->message);
        $this->assertSame($this->demoPreferredDate(), $demo->preferred_date?->toDateString());
        $this->assertSame(['10:00', '14:00'], $demo->preferred_slots);
        $this->assertSame('teams', $demo->meeting_platform);
    }

    public function test_demo_request_requires_qualification_fields(): void
    {
        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'phone' => '',
            'company_size' => '',
            'hires_planned' => '999',
            'hiring_locations' => [],
            'uses_ats' => '',
            'preferred_date' => '',
            'preferred_slots' => [],
            'meeting_platform' => '',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'phone',
                'company_size',
                'hires_planned',
                'hiring_locations',
                'uses_ats',
                'preferred_date',
                'preferred_slots',
                'meeting_platform',
            ]);

        $this->assertSame(0, CompanyDemoRequest::query()->count());
    }

    public function test_demo_request_rejects_weekend_and_invalid_slot(): void
    {
        Mail::fake();
        User::factory()->create(['role' => 'admin']);

        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'preferred_date' => now()->next('Saturday')->toDateString(),
            'preferred_slots' => ['08:00'],
            'meeting_platform' => 'skype',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['preferred_date', 'preferred_slots.0', 'meeting_platform']);
    }

    public function test_company_offer_renders_demo_wizard_steps(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-demo-wizard', false)
            ->assertSee('data-demo-step="1"', false)
            ->assertSee('data-demo-step="2"', false)
            ->assertSee('data-demo-step="3"', false)
            ->assertSee('data-demo-step="4"', false)
            ->assertSee('name="first_name"', false)
            ->assertSee('name="hiring_locations[]"', false)
            ->assertSee('name="preferred_slots[]"', false)
            ->assertSee('name="meeting_platform"', false)
            ->assertSee('data-demo-cancel', false)
            ->assertSee(__('talenma.company_offer.demo_form.cancel'))
            ->assertDontSee('calendly.com', false)
            ->assertSee(__('talenma.company_offer.demo_form.thanks_title'));
    }

    public function test_company_offer_renders_trial_wizard_steps(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-trial-wizard', false)
            ->assertSee('data-trial-step="1"', false)
            ->assertSee('data-trial-step="2"', false)
            ->assertSee('data-trial-step="3"', false)
            ->assertSee('data-trial-cancel', false)
            ->assertSee(__('talenma.company_offer.trial_form.thanks_title'));
    }

    public function test_admin_demo_page_shows_qualification_answers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeDemoRequest([
            'company_size' => '51-200',
            'hires_planned' => '5-10',
            'hiring_locations' => ['ma'],
            'uses_ats' => 'yes',
            'preferred_date' => $this->demoPreferredDate(),
            'preferred_slots' => ['09:00', '11:00'],
            'meeting_platform' => 'zoom',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('data-demo-needs', false)
            ->assertSee('51-200')
            ->assertSee(__('talenma.company_offer.demo_form.locations.ma'))
            ->assertSee('09:00 – 10:00')
            ->assertSee(__('talenma.company_offer.demo_form.platforms.zoom'));
    }

    private function demoPreferredDate(): string
    {
        return CompanyDemoRequest::availableBookingDates()->first()->toDateString();
    }

    private function demoPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@acme.test',
            'phone_country' => 'fr',
            'phone' => '+33123456789',
            'company_name' => 'Acme SAS',
            'company_size' => '11-50',
            'hires_planned' => '1-4',
            'hiring_locations' => ['ma'],
            'hiring_city' => 'Casablanca',
            'uses_ats' => 'no',
            'preferred_date' => $this->demoPreferredDate(),
            'preferred_slots' => ['10:00', '14:00'],
            'meeting_platform' => 'teams',
            'message' => 'Bonjour, je souhaite une démonstration de la plateforme pour notre équipe RH.',
        ], $overrides);
    }

    private function makeDemoRequest(array $overrides = []): CompanyDemoRequest
    {
        return CompanyDemoRequest::query()->create(array_merge([
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'jean@acme.test',
            'phone' => '+33123456789',
            'message' => 'Nous voulons voir la plateforme.',
            'locale' => 'fr',
        ], $overrides));
    }

    public function test_demo_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson(route('company.demo.store'), $this->demoPayload([
            'email' => 'ajax-demo@acme.test',
            'message' => '',
        ]));

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.company_offer.demo_sent'),
            ]);

        $this->assertDatabaseHas('company_demo_requests', [
            'email' => 'ajax-demo@acme.test',
        ]);
        Mail::assertSent(CompanyDemoRequestMail::class);
    }

    public function test_trial_request_is_stored_and_emailed_without_creating_user(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->post(route('company.trial.store'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'company_name' => 'Acme SAS',
            'email' => 'trial@acme.test',
            'phone_country' => 'fr',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success', __('talenma.company_offer.trial_sent'));

        $this->assertDatabaseHas('company_trial_requests', [
            'company_name' => 'Acme SAS',
            'email' => 'trial@acme.test',
            'contact_name' => 'Jean Dupont',
            'status' => CompanyTrialRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'trial@acme.test']);

        Mail::assertSent(CompanyTrialRequestMail::class, function (CompanyTrialRequestMail $mail) use ($admin) {
            return $mail->hasTo($admin->email)
                && $mail->trialRequest->company_name === 'Acme SAS';
        });
    }

    public function test_trial_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson(route('company.trial.store'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'company_name' => 'Acme SAS',
            'email' => 'ajax-trial@acme.test',
            'phone_country' => 'ma',
            'phone' => '0612345678',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.company_offer.trial_sent'),
            ]);

        $this->assertDatabaseHas('company_trial_requests', [
            'email' => 'ajax-trial@acme.test',
            'contact_name' => 'Jean Dupont',
            'phone' => '+212 612345678',
            'status' => CompanyTrialRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'ajax-trial@acme.test']);
        Mail::assertSent(CompanyTrialRequestMail::class);
    }

    public function test_admin_can_list_trial_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        CompanyTrialRequest::query()->create([
            'company_name' => 'Acme Listing',
            'contact_name' => 'Jean Dupont',
            'email' => 'listing@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'status' => CompanyTrialRequest::STATUS_PENDING,
            'locale' => 'fr',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.company-trial-requests.index'))
            ->assertOk()
            ->assertSee('Acme Listing');
    }

    public function test_admin_can_provision_trial_request_and_email_credentials(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $trial = CompanyTrialRequest::query()->create([
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'provision@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'status' => CompanyTrialRequest::STATUS_PENDING,
            'locale' => 'fr',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.company-trial-requests.provision', $trial))
            ->assertRedirect();

        $user = User::query()->where('email', 'provision@acme.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isApproved());
        $this->assertSame('Jean Dupont', $user->companyProfile?->representative_name);
        $this->assertTrue($user->companyProfile?->isOnTrial());

        $trial->refresh();
        $this->assertSame(CompanyTrialRequest::STATUS_PROVISIONED, $trial->status);
        $this->assertSame($user->id, $trial->user_id);

        Mail::assertSent(CompanyApprovedMail::class, function (CompanyApprovedMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && is_string($mail->plainPassword)
                && strlen($mail->plainPassword) >= 8
                && Hash::check($mail->plainPassword, $user->fresh()->password);
        });
    }

    public function test_approving_legacy_pending_company_starts_three_month_trial(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $company = User::factory()->companyOwner()->create([
            'approval_status' => User::APPROVAL_PENDING,
            'approved_at' => null,
            'password' => Hash::make('old-password'),
        ]);
        $profile = CompanyProfile::factory()->create([
            'user_id' => $company->id,
            'is_subscribed' => false,
            'trial_ends_at' => null,
            'subscription_expires_at' => null,
        ]);

        app(UserModerationService::class)->approveCompany($company, $admin);

        $profile->refresh();
        $company->refresh();

        $this->assertTrue($company->isApproved());
        $this->assertTrue($profile->isOnTrial());
        Mail::assertSent(CompanyApprovedMail::class);
    }

    public function test_expired_trial_blocks_talent_pool(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->expired()->create([
            'user_id' => $company->id,
        ]);

        $this->assertFalse($company->fresh()->canAccessTalentPool());
        $this->assertTrue($company->fresh()->companyPlanExpired());

        $this->actingAs($company)
            ->get(route('company.search'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_active_trial_allows_talent_pool(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create([
            'user_id' => $company->id,
        ]);

        $this->assertTrue($company->fresh()->canAccessTalentPool());

        $this->actingAs($company)
            ->get(route('company.search'))
            ->assertOk();
    }
}
