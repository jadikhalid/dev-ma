<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CvBuilderIntroController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->canAccessWorkspaceApps()) {
            return redirect()->route('talent.cv-builder.index');
        }

        return view('career.cv-builder-intro');
    }
}
