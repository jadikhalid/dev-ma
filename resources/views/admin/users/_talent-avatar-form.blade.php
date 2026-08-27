{{-- Expects: $user (talent), $avatarUpdateUrl --}}
@php
    $avatarUpdateUrl = $avatarUpdateUrl ?? route('admin.users.profile.avatar', $user);
@endphp

<form
    id="admin-talent-avatar-card"
    method="POST"
    action="{{ $avatarUpdateUrl }}"
    enctype="multipart/form-data"
    class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6"
    data-ajax
    data-loading-target="admin-talent-avatar-card"
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

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.account.avatar') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.account.avatar_hint') }}</p>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="relative shrink-0">
            <img
                x-show="previewUrl"
                x-cloak
                :src="previewUrl"
                alt="{{ $user->formalDisplayName() }}"
                class="w-28 h-28 rounded-full object-cover shrink-0 ring-2 ring-indigo-100"
            >
            <span
                x-show="!previewUrl"
                class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-indigo-100 text-indigo-700 font-bold text-2xl shrink-0"
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
                <x-input-label for="admin-talent-avatar" :value="__('talenma.account.avatar')" />
                <input
                    id="admin-talent-avatar"
                    name="avatar"
                    type="file"
                    x-ref="input"
                    accept="image/jpeg,image/png,image/webp"
                    @change="onFileChange($event)"
                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                >
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
                    {{ __('talenma.account.avatar_remove') }}
                </label>
            @endif
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
            {{ __('talenma.common.cancel') }}
        </button>
        <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
    </div>
</form>
