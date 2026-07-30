<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;

final class MailSender
{
    public const FROM_NAME = 'Talents MA';

    public static function from(): Address
    {
        return new Address(
            (string) config('mail.from.address'),
            self::FROM_NAME,
        );
    }

    public static function name(): string
    {
        return self::FROM_NAME;
    }
}
