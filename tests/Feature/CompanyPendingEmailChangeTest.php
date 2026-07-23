<?php

namespace Tests\Feature;

use App\Mail\ConfirmPendingEmailMail;
use App\Models\User;
use App\Services\PendingEmailChangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompanyPendingEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    private function approvedOwner(): User
    {
        $user = User::factory()->create([
            'role' => 'company',
            'company_seat' => User::SEAT_OWNER,
            'name' => 'Acme SAS',
            'email' => 'owner@acme.test',
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);

        $user->companyProfile()->create(['country' => 'fr']);

        return $user->fresh();
    }

    public function test_company_owner_email_change_keeps_current_email_until_confirmed(): void
    {
        Mail::fake();

        $user = $this->approvedOwner();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Acme SAS',
                'email' => 'nouveau@acme.test',
            ])
            ->assertRedirect(route('profile.edit', ['panel' => 'account']))
            ->assertSessionHas('toast_success');

        $user->refresh();

        $this->assertSame('owner@acme.test', $user->email);
        $this->assertSame('nouveau@acme.test', $user->pending_email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasPendingEmailChange());

        Mail::assertSent(ConfirmPendingEmailMail::class, function (ConfirmPendingEmailMail $mail) {
            return $mail->hasTo('nouveau@acme.test');
        });
    }

    public function test_confirming_after_cancel_shows_cancelled_toast(): void
    {
        Mail::fake();

        $user = $this->approvedOwner();
        app(PendingEmailChangeService::class)->request($user, 'nouveau@acme.test');

        $mail = Mail::sent(ConfirmPendingEmailMail::class)->first();
        $this->assertNotNull($mail);
        preg_match('#/profile/email/confirm/([^/?#]+)#', $mail->confirmUrl, $matches);
        $token = $matches[1] ?? null;
        $this->assertNotEmpty($token);

        $this->actingAs($user)
            ->post(route('profile.email.cancel'))
            ->assertRedirect(route('profile.edit', ['panel' => 'account']));

        $this->actingAs($user)
            ->get(route('profile.email.confirm', ['token' => $token]))
            ->assertRedirect(route('profile.edit', ['panel' => 'account']))
            ->assertSessionHas('toast_error', __('talenma.account.pending_email_link_cancelled'));

        $user->refresh();
        $this->assertSame('owner@acme.test', $user->email);
        $this->assertNull($user->pending_email);
    }

    public function test_confirming_pending_email_replaces_current_email(): void
    {
        Mail::fake();

        $user = $this->approvedOwner();
        $service = app(PendingEmailChangeService::class);
        $service->request($user, 'nouveau@acme.test');

        $mail = Mail::sent(ConfirmPendingEmailMail::class)->first();
        $this->assertNotNull($mail);

        preg_match('#/profile/email/confirm/([^/?#]+)#', $mail->confirmUrl, $matches);
        $token = $matches[1] ?? null;
        $this->assertNotEmpty($token);

        $this->get(route('profile.email.confirm', ['token' => $token]))
            ->assertRedirect(route('login'))
            ->assertSessionHas('toast_success');

        $user->refresh();

        $this->assertSame('nouveau@acme.test', $user->email);
        $this->assertNull($user->pending_email);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_pending_email_expires_after_five_minutes(): void
    {
        Mail::fake();

        $user = $this->approvedOwner();
        app(PendingEmailChangeService::class)->request($user, 'nouveau@acme.test');

        $user->forceFill([
            'pending_email_expires_at' => now()->subMinute(),
        ])->save();

        $this->actingAs($user)
            ->get(route('profile.edit', ['panel' => 'account']))
            ->assertOk();

        $user->refresh();

        $this->assertNull($user->pending_email);
        $this->assertSame('owner@acme.test', $user->email);
    }
}
