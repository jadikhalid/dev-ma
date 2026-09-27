<?php

namespace App\Mail;

use App\Models\User;
use App\Support\PortalHost;
use Illuminate\Mail\Mailable;

/**
 * Links are generated on the recipient's portal: company accounts can only
 * sign in on the entreprises host, everyone else on the talents host.
 */
abstract class PortalMailable extends Mailable
{
    public function send($mailer)
    {
        return PortalHost::forUser($this->portalRecipient(), fn () => parent::send($mailer));
    }

    protected function portalRecipient(): ?User
    {
        $address = $this->to[0]['address'] ?? null;

        if (! is_string($address) || $address === '') {
            return null;
        }

        return User::query()->where('email', strtolower(trim($address)))->first();
    }
}
