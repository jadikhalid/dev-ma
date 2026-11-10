<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Support\PortalHost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact', [
            'isCompanyContact' => PortalHost::isCompanyHost(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (filled($request->input('website'))) {
            return response()->json([
                'message' => __('talenma.contact_page.form.sent'),
            ]);
        }

        $audience = PortalHost::isCompanyHost() ? 'company' : 'talent';

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:filter', 'max:255'],
            'role' => ['required', 'string', 'in:talent,company,other'],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'consent' => ['accepted'],
        ], [
            'name.required' => __('talenma.contact_page.form.name_required'),
            'name.min' => __('talenma.contact_page.form.name_min'),
            'email.required' => __('talenma.contact_page.form.email_required'),
            'email.email' => __('talenma.contact_page.form.email_invalid'),
            'role.required' => __('talenma.contact_page.form.role_required'),
            'role.in' => __('talenma.contact_page.form.role_invalid'),
            'subject.required' => __('talenma.contact_page.form.subject_required'),
            'subject.min' => __('talenma.contact_page.form.subject_min'),
            'message.required' => __('talenma.contact_page.form.message_required'),
            'message.min' => __('talenma.contact_page.form.message_min'),
            'consent.accepted' => __('talenma.contact_page.form.consent_required'),
        ]);

        // On company host, force company role even if the field was tampered with.
        if ($audience === 'company') {
            $data['role'] = 'company';
        }

        $recipient = (string) config('talenma.contact.recipient');

        try {
            Mail::to($recipient)->send(new ContactMessageMail(
                name: trim($data['name']),
                email: strtolower(trim($data['email'])),
                role: $data['role'],
                subjectLine: trim($data['subject']),
                body: trim($data['message']),
                audience: $audience,
            ));
        } catch (Throwable) {
            return response()->json([
                'message' => __('talenma.contact_page.form.error'),
            ], 500);
        }

        return response()->json([
            'message' => __('talenma.contact_page.form.sent'),
        ]);
    }
}
