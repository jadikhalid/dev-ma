<?php

namespace App\Http\Controllers;

use App\Mail\JadiDigitalContactMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class JadiDigitalContactController extends Controller
{
    public const RECIPIENT = 'jadi.khalid@talentsdumaroc.com';

    public function store(Request $request): JsonResponse
    {
        // Honeypot : bot fills hidden field → fake success, no mail.
        if (filled($request->input('website'))) {
            return response()->json([
                'message' => __('talenma.jadi_page.contact_form.sent'),
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:filter', 'max:255'],
            'company' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
        ], [
            'name.required' => __('talenma.jadi_page.contact_form.name_required'),
            'name.min' => __('talenma.jadi_page.contact_form.name_min'),
            'email.required' => __('talenma.jadi_page.contact_form.email_required'),
            'email.email' => __('talenma.jadi_page.contact_form.email_invalid'),
            'message.required' => __('talenma.jadi_page.contact_form.message_required'),
            'message.min' => __('talenma.jadi_page.contact_form.message_min'),
        ]);

        try {
            Mail::to(self::RECIPIENT)->send(new JadiDigitalContactMail(
                name: trim($data['name']),
                email: strtolower(trim($data['email'])),
                company: filled($data['company'] ?? null) ? trim($data['company']) : null,
                body: trim($data['message']),
            ));
        } catch (Throwable) {
            return response()->json([
                'message' => __('talenma.jadi_page.contact_form.error'),
            ], 500);
        }

        return response()->json([
            'message' => __('talenma.jadi_page.contact_form.sent'),
        ]);
    }
}
