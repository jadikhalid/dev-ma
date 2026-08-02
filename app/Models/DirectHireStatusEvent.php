<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'direct_hire_request_id',
    'event',
    'status',
    'comment',
    'actor_user_id',
    'created_at',
])]
class DirectHireStatusEvent extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_PROPOSED = 'proposed';

    public const EVENT_TALENT_DECISION = 'talent_decision';

    public const EVENT_DEFERRAL_ACKNOWLEDGED = 'deferral_acknowledged';

    public const EVENT_WITHDRAWN = 'withdrawn';

    public const EVENT_CLOSED = 'closed';

    public function request(): BelongsTo
    {
        return $this->belongsTo(DirectHireRequest::class, 'direct_hire_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function label(): string
    {
        return match ($this->event) {
            self::EVENT_PROPOSED => __('talenma.direct_hire.history_proposed'),
            self::EVENT_TALENT_DECISION => match ($this->status) {
                DirectHireRequest::STATUS_IN_PROCESS => __('talenma.direct_hire.history_accepted'),
                DirectHireRequest::STATUS_DECLINED => __('talenma.direct_hire.history_declined'),
                DirectHireRequest::STATUS_DEFERRED => __('talenma.direct_hire.history_deferred'),
                default => __('talenma.direct_hire.talent_decision'),
            },
            self::EVENT_DEFERRAL_ACKNOWLEDGED => __('talenma.direct_hire.history_deferral_acknowledged'),
            self::EVENT_WITHDRAWN => __('talenma.direct_hire.history_withdrawn'),
            self::EVENT_CLOSED => match ($this->status) {
                DirectHireRequest::STATUS_HIRED => __('talenma.direct_hire.history_closed_hired'),
                DirectHireRequest::STATUS_CLOSED_NEGATIVE => __('talenma.direct_hire.history_closed_negative'),
                default => __('talenma.direct_hire.history_closed'),
            },
            default => $this->event,
        };
    }

    public function actorLabel(): ?string
    {
        $this->loadMissing(['actor', 'request.company', 'request.companyProfile', 'request.talent', 'request.initiatedBy']);

        $actor = $this->actor;
        $request = $this->request;

        if (! $actor || ! $request) {
            return null;
        }

        if ($actor->isTalent()) {
            return $request->talentDisplayName();
        }

        if ($actor->isStaff()) {
            return $actor->name ?: __('talenma.direct_hire.platform_employer_name');
        }

        if ($actor->isCompany()) {
            return $request->companyDisplayName();
        }

        return $actor->name;
    }
}
