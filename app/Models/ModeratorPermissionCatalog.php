<?php

namespace App\Models;

final class ModeratorPermissionCatalog
{
    public const ACCOUNTS_VIEW = 'accounts.view';

    public const ACCOUNTS_APPROVE = 'accounts.approve';

    public const ACCOUNTS_REJECT = 'accounts.reject';

    public const ACCOUNTS_DELETE = 'accounts.delete';

    public const PROFILES_EDIT = 'profiles.edit';

    public const SOURCING_MANAGE = 'sourcing.manage';

    public const DIRECT_HIRE_MANAGE = 'direct_hire.manage';

    public const JOBS_MANAGE = 'jobs.manage';

    public const STAFF_MESSAGES_MANAGE = 'staff_messages.manage';

    public const PUBLICATIONS_MANAGE = 'publications.manage';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::ACCOUNTS_VIEW,
            self::ACCOUNTS_APPROVE,
            self::ACCOUNTS_REJECT,
            self::ACCOUNTS_DELETE,
            self::PROFILES_EDIT,
            self::SOURCING_MANAGE,
            self::DIRECT_HIRE_MANAGE,
            self::JOBS_MANAGE,
            self::STAFF_MESSAGES_MANAGE,
            self::PUBLICATIONS_MANAGE,
        ];
    }

    public static function isValid(string $permission): bool
    {
        return in_array($permission, self::all(), true);
    }

    /**
     * @return list<array{key: string, label: string, description: string}>
     */
    public static function options(): array
    {
        // Les clés de permission contiennent un point : on récupère le groupe
        // entier pour l'indexer littéralement, sinon Laravel les résout comme
        // des tableaux imbriqués.
        $translations = __('talenma.admin.users.moderator_permissions');
        $translations = is_array($translations) ? $translations : [];

        return array_map(
            fn (string $key) => [
                'key' => $key,
                'label' => $translations[$key]['label'] ?? $key,
                'description' => $translations[$key]['description'] ?? '',
            ],
            self::all(),
        );
    }
}
