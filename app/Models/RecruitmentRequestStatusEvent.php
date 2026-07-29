<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'recruitment_request_id',
    'event',
    'status',
    'actor_user_id',
    'created_at',
])]
class RecruitmentRequestStatusEvent extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_SUBMITTED = 'submitted';

    public const EVENT_STATUS_CHANGED = 'status_changed';

    public const EVENT_COMMENT_UPDATED = 'comment_updated';

    public function request(): BelongsTo
    {
        return $this->belongsTo(RecruitmentRequest::class, 'recruitment_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function label(bool $forStaff = false, ?string $mode = null): string
    {
        $mode ??= $this->request?->mode ?? RecruitmentRequest::MODE_OPEN;
        $named = $mode === RecruitmentRequest::MODE_NAMED;
        $suffix = $named ? 'named' : 'open';

        if ($this->event === self::EVENT_SUBMITTED) {
            return $forStaff
                ? __('talenma.recruitment.history_submitted_'.$suffix.'_staff')
                : __('talenma.recruitment.history_submitted_'.$suffix);
        }

        if ($this->event === self::EVENT_COMMENT_UPDATED) {
            return __('talenma.recruitment.history_comment');
        }

        return match ($this->status) {
            RecruitmentRequest::STATUS_IN_PROGRESS => __('talenma.recruitment.history_taken'),
            RecruitmentRequest::STATUS_COMPLETED_SUCCESSFUL, RecruitmentRequest::STATUS_COMPLETED => __('talenma.recruitment.history_closed_successful'),
            RecruitmentRequest::STATUS_COMPLETED_UNSUCCESSFUL, RecruitmentRequest::STATUS_CANCELLED => __('talenma.recruitment.history_closed_unsuccessful'),
            RecruitmentRequest::STATUS_PENDING => __('talenma.recruitment.history_reopened'),
            default => __('talenma.recruitment.status_'.$this->status),
        };
    }
}
