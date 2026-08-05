<?php

namespace App\Http\Controllers;

use App\Services\ModeratorAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ModeratorModeController extends Controller
{
    public function __construct(private ModeratorAssignmentService $assignments) {}

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:talent,moderator'],
        ]);

        $asModerator = $validated['mode'] === 'moderator';
        $this->assignments->setActingMode($request->user(), $asModerator);

        return redirect()
            ->route('dashboard')
            ->with('status', $asModerator
                ? __('talenma.admin.users.moderator_mode_enabled')
                : __('talenma.admin.users.moderator_mode_disabled'));
    }
}
