@php
    $pendingRegistrationEmail = session('pending_registration_email');
@endphp

<x-guest-layout :viewport-fit="! $pendingRegistrationEmail" auth-phone-logo>
    <x-slot name="title">
        {{ $pendingRegistrationEmail ? __('talenma.auth.verify_email_title') : __('talenma.auth.register_title_talent') }}
    </x-slot>
    @unless ($pendingRegistrationEmail)
        <x-slot name="description">{{ __('talenma.auth.register_desc_talent') }}</x-slot>
    @endunless

    <x-toast-stack :persistent="! $pendingRegistrationEmail" />

    @if ($pendingRegistrationEmail)
        @include('auth.partials.pending-registration-verification', [
            'pendingEmail' => $pendingRegistrationEmail,
        ])
    @else
        @include('auth.partials.register-wizard-form', [
            'formClass' => 'flex flex-col sm:h-full sm:min-h-0',
        ])
    @endif
</x-guest-layout>
