<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Support\PortalHost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_contact_page_renders_form(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee(__('talenma.contact_page.title_accent'), false)
            ->assertSee(__('talenma.contact_page.form_title'), false)
            ->assertSee(__('talenma.contact_page.form.role_talent'), false)
            ->assertSee('id="contact-form-card"', false)
            ->assertSee(route('contact.store'), false)
            ->assertSee(config('talenma.contact.email'), false)
            ->assertSee('page-contact', false)
            ->assertDontSee(__('talenma.contact_page.home').' / '.__('talenma.footer.contact'), false)
            ->assertDontSee(__('talenma.company_contact.title_accent'), false)
            ->assertDontSee(__('talenma.company_contact.secondary_label'), false);
    }

    public function test_company_contact_page_renders_company_copy(): void
    {
        $this->get('http://'.PortalHost::companyHost().'/contact')
            ->assertOk()
            ->assertSee(__('talenma.company_contact.title_accent'), false)
            ->assertSee(__('talenma.company_contact.form_title'), false)
            ->assertSee(__('talenma.company_contact.faq_subtitle'), false)
            ->assertSee(__('talenma.company_contact.secondary_label'), false)
            ->assertSee(route('company.demo'), false)
            ->assertSee('data-contact-hero', false)
            ->assertDontSee(__('talenma.contact_page.title_accent'), false);
    }

    public function test_contact_form_sends_mail_via_ajax(): void
    {
        Mail::fake();

        $this->postJson(route('contact.store'), [
            'name' => 'Sara El Amrani',
            'email' => 'sara@example.com',
            'role' => 'talent',
            'subject' => 'Question sur mon profil',
            'message' => 'Bonjour, j’aimerais savoir comment mettre à jour mon CV sur la plateforme.',
            'consent' => '1',
        ])
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.contact_page.form.sent'),
            ]);

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->hasTo(config('talenma.contact.recipient'))
                && $mail->name === 'Sara El Amrani'
                && $mail->email === 'sara@example.com'
                && $mail->role === 'talent'
                && $mail->audience === 'talent'
                && $mail->subjectLine === 'Question sur mon profil';
        });
    }

    public function test_company_host_forces_company_role(): void
    {
        Mail::fake();

        $this->postJson('http://'.PortalHost::companyHost().'/contact', [
            'name' => 'Recruteur Acme',
            'email' => 'rh@acme.test',
            'role' => 'talent',
            'subject' => 'Demande catalogue',
            'message' => 'Bonjour, nous souhaitons accéder au catalogue de talents vérifiés.',
            'consent' => '1',
        ])
            ->assertOk();

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->role === 'company'
                && $mail->audience === 'company';
        });
    }

    public function test_contact_form_validates_fields(): void
    {
        Mail::fake();

        $this->postJson(route('contact.store'), [
            'name' => 'A',
            'email' => 'not-an-email',
            'role' => 'talent',
            'subject' => 'Hi',
            'message' => 'Trop court',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message', 'consent']);

        Mail::assertNothingSent();
    }

    public function test_contact_honeypot_skips_mail(): void
    {
        Mail::fake();

        $this->postJson(route('contact.store'), [
            'name' => 'Bot User',
            'email' => 'bot@example.com',
            'role' => 'other',
            'subject' => 'Spam subject line',
            'message' => 'Message suffisamment long pour passer la validation.',
            'consent' => '1',
            'website' => 'https://spam.example',
        ])
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.contact_page.form.sent'),
            ]);

        Mail::assertNothingSent();
    }
}
