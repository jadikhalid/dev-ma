@props(['disabled' => false])

@php
    $isPassword = ($attributes->get('type') ?? 'text') === 'password';
    $inputClass = 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';
@endphp

@if ($isPassword)
    <div class="relative" x-data="{ show: false }">
        <input
            type="password"
            x-bind:type="show ? 'text' : 'password'"
            @disabled($disabled)
            {{ $attributes->merge(['class' => $inputClass.' pr-10'])->except('type') }}
        >
        <button
            type="button"
            tabindex="-1"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 disabled:opacity-50"
            x-on:click="show = !show"
            x-bind:aria-label="show ? @js(__('talenma.auth.hide_password')) : @js(__('talenma.auth.show_password'))"
            x-bind:aria-pressed="show"
            @disabled($disabled)
        >
            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 2.4 3.997 4.243 7.066 5.177 1.943.49 3.98.49 5.923 0 3.069-.934 5.774-2.777 7.066-5.177a10.48 10.48 0 0 0-2.046-3.777M9.88 9.88a3 3 0 1 0 4.24 4.24M3 3l18 18" />
            </svg>
        </button>
    </div>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => $inputClass]) }}>
@endif
