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

        <form method="post" action="{{ route('profile.contact.update') }}" class="mt-6 space-y-6">
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
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('linkedin_url')" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('talenma.common.save') }}</x-primary-button>

                @if (session('status') === 'contact-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-medium">
                        {{ __('talenma.account.contact_saved') }}
                    </p>
                @endif
            </div>
        </form>
    </section>
@endif
