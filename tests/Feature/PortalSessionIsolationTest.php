<?php

namespace Tests\Feature;

use App\Mail\PortalMailable;
use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortalSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_links_to_company_use_company_portal(): void
    {
        $company = User::factory()->companyOwner()->create();

        $html = $this->sendDashboardLinkMailTo($company);

        $this->assertStringContainsString(PortalHost::companyUrl('/dashboard'), $html);
    }

    public function test_mail_links_to_talent_use_talents_host_even_from_company_portal(): void
    {
        $talent = User::factory()->talent()->create();

        $html = PortalHost::onCompanyHost(fn () => $this->sendDashboardLinkMailTo($talent));

        $this->assertStringContainsString(rtrim(PortalHost::wwwRootUrl(), '/').'/dashboard', $html);
        $this->assertStringNotContainsString(PortalHost::companyHost(), $html);
    }

    public function test_guest_opening_company_page_on_talents_host_is_sent_to_company_portal(): void
    {
        $www = config('talenma.hosts.www');

        $this->get('http://'.$www.'/company/direct-hire/12?tab=chat')
            ->assertRedirect(PortalHost::companyUrl('/company/direct-hire/12?tab=chat'));

        $this->get('http://'.$www.'/dashboard')
            ->assertRedirect(route('login'));
    }

    private function sendDashboardLinkMailTo(User $recipient): string
    {
        config(['mail.default' => 'array']);

        Mail::to($recipient->email)->send(new class extends PortalMailable
        {
            public function envelope(): Envelope
            {
                return new Envelope(subject: 'Test');
            }

            public function content(): Content
            {
                return new Content(htmlString: '<a href="'.route('dashboard').'">x</a>');
            }
        });

        $message = app('mailer')->getSymfonyTransport()->messages()->last();

        return $message->getOriginalMessage()->getHtmlBody();
    }
}
