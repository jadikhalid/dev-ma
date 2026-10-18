<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AtsScoreIntroController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->canAccessWorkspaceApps()) {
            return redirect()->route('talent.ats-score.index');
        }

        return view('career.ats-score-intro');
    }
}
