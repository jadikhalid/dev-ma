<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CvBuilderGateController extends Controller
{
    /**
     * Guests reach this from the homepage announcement; auth middleware stores the intended URL for post-login return.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isTalent()) {
            return redirect()->route('talent.cv-builder.index');
        }

        return redirect()->route($user->homeRouteName());
    }
}
