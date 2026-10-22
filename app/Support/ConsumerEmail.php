<?php

namespace App\Support;

class ConsumerEmail
{
    /**
     * Premier label du domaine : gmail.com, yahoo.fr, outlook.co.uk, etc.
     *
     * @return list<string>
     */
    public static function roots(): array
    {
        return [
            'gmail',
            'googlemail',
            'yahoo',
            'ymail',
            'rocketmail',
            'outlook',
            'hotmail',
            'live',
            'msn',
            'icloud',
            'aol',
            'protonmail',
            'gmx',
        ];
    }

    /**
     * Domaines grand public dont le premier label ne suffit pas.
     *
     * @return list<string>
     */
    public static function domains(): array
    {
        return [
            'me.com',
            'mac.com',
            'proton.me',
            'pm.me',
            'mail.com',
            'orange.fr',
            'wanadoo.fr',
            'free.fr',
            'laposte.net',
            'sfr.fr',
            'bbox.fr',
            'neuf.fr',
            'numericable.fr',
        ];
    }

    public static function isPersonal(string $email): bool
    {
        $domain = strtolower(ltrim(strrchr($email, '@') ?: '', '@'));

        if ($domain === '' || ! str_contains($domain, '.')) {
            return false;
        }

        foreach (self::domains() as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.'.$blocked)) {
                return true;
            }
        }

        $root = explode('.', $domain)[0] ?? '';

        return in_array($root, self::roots(), true);
    }
}
