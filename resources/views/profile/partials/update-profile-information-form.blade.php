@php
    $isCompanyOwner = Auth::user()->isCompanyOwner();
    $useCompanyBranding = $isCompanyOwner;
@endphp
<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            {{ $useCompanyBranding
                ? __('talenma.account.personal_info_title_company')
                : __('talenma.account.personal_info_title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ $useCompanyBranding
                ? __('talenma.account.personal_info_desc_company')
                : __('talenma.account.personal_info_desc') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        id="account-profile-form"
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="relative mt-6 space-y-6"
        data-ajax
        data-loading-target="account-profile-card"
        data-error-message="{{ __('talenma.common.save_error') }}"
        novalidate
        x-data="avatarPreview({
            initialUrl: @js($user->avatarUrl()),
            initials: @js($user->initials()),
            maxBytes: {{ 2 * 1024 * 1024 }},
            maxSize: {{ \App\Services\AvatarService::MAX_SIZE }},
            allowedMimes: @js(\App\Services\AvatarService::ALLOWED_MIMES),
            messages: {
                invalidType: @js(__('talenma.account.avatar_invalid_type')),
                tooLarge: @js(__('talenma.account.avatar_too_large')),
            },
        })"
    >
        @csrf
        @method('patch')

        <div class="flex flex-col sm:flex-row sm:items-center gap-5 pb-6 border-b border-gray-100">
            <div class="relative shrink-0">
                <img
                    x-show="previewUrl"
                    x-cloak
                    :src="previewUrl"
                    alt="{{ $user->name }}"
                    class="w-32 h-32 rounded-full object-cover shrink-0 ring-2 ring-indigo-100"
                >
                <span
                    x-show="!previewUrl"
                    class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-indigo-100 text-indigo-700 font-bold text-3xl shrink-0"
                    aria-hidden="true"
                    x-text="initials"
                ></span>
                <span
                    x-show="processing"
                    x-cloak
                    class="absolute inset-0 flex items-center justify-center rounded-full bg-white/70 text-xs font-semibold text-indigo-700"
                >…</span>
            </div>
            <div class="flex-1 space-y-3">
                <div>
                    <x-input-label
                        for="avatar"
                        :value="$useCompanyBranding ? __('talenma.account.avatar_company') : __('talenma.account.avatar')"
                    />
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        x-ref="input"
                        accept="image/jpeg,image/png,image/webp"
                        @change="onFileChange($event)"
                        class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    >
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $useCompanyBranding ? __('talenma.account.avatar_hint_company') : __('talenma.account.avatar_hint') }}
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>
                @if ($user->avatar_path)
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input
                            type="checkbox"
                            name="remove_avatar"
                            value="1"
                            x-ref="removeAvatar"
                            @change="onRemoveToggle($event)"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        {{ $useCompanyBranding ? __('talenma.account.avatar_remove_company') : __('talenma.account.avatar_remove') }}
                    </label>
                @endif
            </div>
        </div>

        @if ($user->isCompanyMember())
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="first_name" :value="__('talenma.auth.first_name')" />
                    <x-text-input
                        id="first_name"
                        name="first_name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('first_name', $user->first_name)"
                        required
                        autofocus
                        autocomplete="given-name"
                        data-required
                        data-required-message="{{ __('talenma.auth.first_name') }}"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>
                <div>
                    <x-input-label for="last_name" :value="__('talenma.auth.last_name')" />
                    <x-text-input
                        id="last_name"
                        name="last_name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('last_name', $user->last_name)"
                        required
                        autocomplete="family-name"
                        data-required
                        data-required-message="{{ __('talenma.auth.last_name') }}"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>
            </div>
        @else
            <div>
                <x-input-label
                    for="name"
                    :value="$useCompanyBranding ? __('talenma.account.name_company') : __('talenma.account.name')"
                />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                    data-required
                    data-required-message="{{ $useCompanyBranding ? __('talenma.account.name_company') : __('talenma.account.name') }}"
                />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>
        @endif

        <div>
            <x-input-label
                for="email"
                :value="$useCompanyBranding ? __('talenma.account.email_company') : __('talenma.account.email')"
            />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
                data-required
                data-required-message="{{ $useCompanyBranding ? __('talenma.account.email_company') : __('talenma.account.email') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($useCompanyBranding && $user->hasPendingEmailChange())
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50/60 px-3.5 py-3 space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">
                        {{ __('talenma.account.pending_email_label') }}
                    </p>
                    <input
                        type="email"
                        value="{{ $user->pending_email }}"
                        disabled
                        class="block w-full rounded-md border-gray-200 bg-gray-100 text-gray-400 shadow-sm text-sm cursor-not-allowed"
                    >
                    <p class="text-xs text-amber-800/80">
                        {{ __('talenma.account.pending_email_hint', ['minutes' => \App\Services\PendingEmailChangeService::TTL_MINUTES]) }}
                    </p>
                    <button
                        type="submit"
                        form="cancel-pending-email-form"
                        class="text-xs font-semibold text-amber-900 underline hover:text-amber-700"
                    >
                        {{ __('talenma.account.pending_email_cancel') }}
                    </button>
                </div>
            @elseif ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail() && ! $useCompanyBranding)
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('talenma.account.email_unverified') }}
                        <button form="send-verification" class="underline text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.account.resend_verification') }}
                        </button>
                    </p>
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
            <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
                {{ __('talenma.common.cancel') }}
            </button>
            <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
        </div>
    </form>

    @if ($useCompanyBranding && $user->hasPendingEmailChange())
        <form id="cancel-pending-email-form" method="post" action="{{ route('profile.email.cancel') }}" class="hidden">
            @csrf
        </form>
    @endif
</section>
