@if ($user->isCompanyOwner())
    @php
        $companyProfile = $user->companyProfile;
    @endphp
    <section>
        <header>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.company.section_contact') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('talenma.company.section_contact_desc') }}</p>
            <p class="mt-2 text-xs text-gray-500">{{ __('talenma.company.section_contact_email_hint', ['email' => $user->email]) }}</p>
        </header>

        <form
            method="post"
            action="{{ route('profile.contact.update') }}"
            enctype="multipart/form-data"
            class="relative mt-6 space-y-6"
            data-ajax
            data-loading-target="account-contact-card"
            data-error-message="{{ __('talenma.common.save_error') }}"
            novalidate
            x-data="avatarPreview({
                initialUrl: @js($companyProfile?->representativePhotoUrl()),
                initials: @js($companyProfile?->representativeInitials() ?? '—'),
                maxBytes: {{ 2 * 1024 * 1024 }},
                maxSize: {{ \App\Services\AvatarService::MAX_SIZE }},
                allowedMimes: @js(\App\Services\AvatarService::ALLOWED_MIMES),
                messages: {
                    invalidType: @js(__('talenma.company.representative_photo_invalid_type')),
                    tooLarge: @js(__('talenma.company.representative_photo_too_large')),
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
                        alt="{{ $companyProfile?->representative_name ?: __('talenma.company.section_contact') }}"
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
                            for="representative_photo"
                            :value="__('talenma.company.representative_photo')"
                        />
                        <input
                            id="representative_photo"
                            name="representative_photo"
                            type="file"
                            x-ref="input"
                            accept="image/jpeg,image/png,image/webp"
                            @change="onFileChange($event)"
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('talenma.company.representative_photo_hint') }}
                        </p>
                        <x-input-error class="mt-2" :messages="$errors->get('representative_photo')" />
                    </div>
                    @if ($companyProfile?->representative_photo_path)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input
                                type="checkbox"
                                name="remove_representative_photo"
                                value="1"
                                x-ref="removeAvatar"
                                @change="onRemoveToggle($event)"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            {{ __('talenma.company.representative_photo_remove') }}
                        </label>
                    @endif
                </div>
            </div>

            <div>
                <x-input-label for="representative_name" :value="__('talenma.company.contact_full_name')" />
                <x-text-input
                    id="representative_name"
                    name="representative_name"
                    class="mt-1 block w-full"
                    :value="old('representative_name', $companyProfile?->representative_name)"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company.representative_name_required') }}"
                />
                <x-input-error class="mt-2" :messages="$errors->get('representative_name')" />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('talenma.talent.phone')" />
                    <x-text-input
                        id="phone"
                        name="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        :value="old('phone', $companyProfile?->phone)"
                        placeholder="+33 6 00 00 00 00"
                        data-phone
                        data-phone-message="{{ __('talenma.company.phone_invalid') }}"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
                <div>
                    <x-input-label for="linkedin_url" :value="__('talenma.company.linkedin')" />
                    <x-text-input
                        id="linkedin_url"
                        name="linkedin_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('linkedin_url', $companyProfile?->linkedin_url)"
                        placeholder="https://linkedin.com/in/..."
                        data-url
                        data-url-message="{{ __('talenma.company.linkedin_invalid') }}"
                        data-url-host="linkedin.com"
                        data-url-host-message="{{ __('talenma.company.linkedin_host') }}"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('linkedin_url')" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
                    {{ __('talenma.common.cancel') }}
                </button>
                <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
            </div>
        </form>
    </section>
@endif
