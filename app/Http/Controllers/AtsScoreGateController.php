<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AtsScoreGateController extends Controller
{
    /**
     * Guests reach this from the public apps launcher; auth middleware stores the intended URL for post-login return.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->canAccessWorkspaceApps()) {
            return redirect()->route('talent.ats-score.index');
        }

        return redirect()->route($user->homeRouteName());
    }
}
