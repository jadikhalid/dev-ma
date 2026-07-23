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
            class="relative mt-6 space-y-6"
            data-ajax
            data-loading-target="account-contact-card"
            data-error-message="{{ __('talenma.common.save_error') }}"
            novalidate
        >
            @csrf
            @method('patch')

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
