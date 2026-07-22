<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            {{ $user->isCompany()
                ? __('talenma.account.personal_info_title_company')
                : __('talenma.account.personal_info_title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ $user->isCompany()
                ? __('talenma.account.personal_info_desc_company')
                : __('talenma.account.personal_info_desc') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-6"
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
                        :value="$user->isCompany() ? __('talenma.account.avatar_company') : __('talenma.account.avatar')"
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
                        {{ $user->isCompany() ? __('talenma.account.avatar_hint_company') : __('talenma.account.avatar_hint') }}
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
                        {{ $user->isCompany() ? __('talenma.account.avatar_remove_company') : __('talenma.account.avatar_remove') }}
                    </label>
                @endif
            </div>
        </div>

        <div>
            <x-input-label
                for="name"
                :value="$user->isCompany() ? __('talenma.account.name_company') : __('talenma.account.name')"
            />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('talenma.account.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('talenma.account.email_unverified') }}
                        <button form="send-verification" class="underline text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.account.resend_verification') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('talenma.account.verification_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('talenma.common.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-medium">
                    {{ __('talenma.account.saved') }}
                </p>
            @endif
        </div>
    </form>
</section>
