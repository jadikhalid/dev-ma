<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountStatusController extends Controller
{
    public function pending(Request $request): View
    {
        return view('account.pending', [
            'user' => $request->user(),
        ]);
    }

    public function rejected(Request $request): View
    {
        return view('account.rejected', [
            'user' => $request->user(),
        ]);
    }
}
