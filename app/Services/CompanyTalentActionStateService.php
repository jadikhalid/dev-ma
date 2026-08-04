<?php

namespace App\Services;

use App\Models\User;

class CompanyTalentActionStateService
{
    public function __construct(
        private DirectHireService $directHires,
        private RecruitmentRequestService $recruitmentRequests,
    ) {}

    /**
     * Hire CTAs + lock state for a company/talent pair (catalog card & profile drawer).
     * Org-wide: an open/locked named intermediation or direct hire for a talent blocks both CTAs for the whole team.
     *
     * @return array<string, mixed>
     */
    public function for(User $company, User $talent): array
    {
        $namedLock = $this->recruitmentRequests->activeNamedLockForTalent($company, $talent);
        $directHireLock = $this->directHires->activeHireLockForTalent($company, $talent);
        $talentLocked = $namedLock !== null || $directHireLock !== null;

        $canProposeGlobally = $this->directHires->companyCanPropose($company);
        $canPropose = $this->directHires->companyCanProposeToTalent($company, $talent);
        $directHireHint = $this->directHires->companyProposeDisabledHint($company, $talent);

        $canRequestNamed = $this->recruitmentRequests->companyCanRequestNamedForTalent($company, $talent);
        $namedHint = $this->recruitmentRequests->namedRequestDisabledHint($company, $talent);
        $existingNamed = (! $canRequestNamed)
            ? ($this->recruitmentRequests->existingNamedRequestForCompanyTalent($company, $talent)
                ?: $namedLock)
            : null;

        return [
            'talent_id' => (int) $talent->id,
            'talent_locked' => $talentLocked,
            'can_request_named' => $canRequestNamed,
            'named_request_disabled_hint' => $namedHint,
            'recruitment_url' => $canRequestNamed
                ? route('recruitment.create', $talent)
                : ($existingNamed ? route('sourcing.show', $existingNamed) : null),
            'named_unlock_url' => $namedLock
                ? route('sourcing.unlock-talent', $namedLock)
                : null,
            'direct_hire_url' => route('company.direct-hire.create', $talent),
            'can_propose_direct_hire' => $canPropose,
            'direct_hire_disabled_hint' => $directHireHint,
            'direct_hire_unlock_url' => $directHireLock
                ? route('company.direct-hire.unlock-talent', $directHireLock)
                : null,
            'can_propose_direct_hire_globally' => $canProposeGlobally,
        ];
    }
}
