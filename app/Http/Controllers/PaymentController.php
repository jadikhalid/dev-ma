<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function simulate(Request $request)
    {
        $user = $request->user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        if (! app()->environment('local', 'testing')) {
            abort(403, 'Paiement simulé disponible uniquement en environnement local.');
        }

        $user->update([
            'is_subscribed' => true,
            'subscription_expires_at' => Carbon::now()->addYear(),
        ]);

        return redirect()->route('dashboard')->with(
            'payment_success',
            __('talenma.dashboard.talent.payment_success')
        );
    }
}
