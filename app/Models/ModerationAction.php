<?php

namespace App\Models;

/**
 * Identifiants d'actions de modération de comptes (exécution immédiate).
 */
final class ModerationAction
{
    public const APPROVE_TALENT = 'approve_talent';

    public const REJECT_TALENT = 'reject_talent';

    public const APPROVE_COMPANY = 'approve_company';

    public const REJECT_COMPANY = 'reject_company';

    public const DELETE_USER = 'delete_user';

    public const CREATE_USER = 'create_user';

    public const GRANT_MODERATOR = 'grant_moderator';

    public const REVOKE_MODERATOR = 'revoke_moderator';
}
