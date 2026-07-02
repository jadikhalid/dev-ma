<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <x-user-avatar :user="$user" size="md" />
            <div>
                <h2 class="text-xl font-bold">{{ __('talenma.talent.profile_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.talent.profile_subtitle') }}</p>
                <a href="{{ route('profile.edit') }}" class="mt-1 inline-block text-xs text-indigo-600 font-medium hover:text-indigo-800">{{ __('talenma.talent.change_photo') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.talent.updated') }}</div>
        @endif

        @if ($profile->registration_description || $profile->documents->isNotEmpty())
            <section class="mb-6 bg-white rounded-2xl border p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.registration_dossier') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.registration_dossier_desc') }}</p>

                @if ($profile->sectorLabel())
                    <p class="mt-4 text-sm"><span class="font-medium text-gray-700">{{ __('talenma.talent.registration_sector') }} :</span> {{ $profile->sectorLabel() }}</p>
                @endif

                @if ($profile->registration_description)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700">{{ __('talenma.talent.registration_presentation') }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $profile->registration_description }}</p>
                    </div>
                @endif

                @if ($profile->documents->isNotEmpty())
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-700">{{ __('talenma.talent.registration_files') }}</p>
                        <ul class="mt-2 space-y-2">
                            @foreach ($profile->documents as $document)
                                <li>
                                    <a href="{{ $document->url() }}" target="_blank" rel="noopener" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        📎 {{ $document->original_name }} <span class="text-gray-400">({{ $document->formattedSize() }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>
        @endif

        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-xl text-sm">
            {{ __('talenma.talent.tip') }}
        </div>

        <form method="POST" action="{{ route('profile.details.update') }}" class="space-y-8">
            @csrf

            <section class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_profession') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.section_profession_desc') }}</p>
                </div>

                <x-profile-profession-fields
                    :sectors="$professionSectors"
                    :sector="$sectorSlug"
                    :profession="$professionSlug"
                    :specialization="$specialization"
                />

                <div>
                    <x-input-label for="title" :value="__('talenma.talent.title')" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $profile->title)" :placeholder="__('talenma.talent.title_placeholder')" required />
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.talent.title_hint') }}</p>
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
            </section>

            <section class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_presentation') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.section_presentation_desc') }}</p>
                </div>

                <div>
                    <x-input-label for="bio" :value="__('talenma.talent.bio')" />
                    <textarea id="bio" name="bio" rows="6" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required placeholder="{{ __('talenma.talent.bio_placeholder') }}">{{ old('bio', $profile->bio) }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="experience_years" :value="__('talenma.talent.experience')" />
                        <x-text-input id="experience_years" name="experience_years" type="number" class="mt-1 block w-full" :value="old('experience_years', $profile->experience_years)" min="0" max="50" required />
                        <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="education_level" :value="__('talenma.talent.education')" />
                        <select id="education_level" name="education_level" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="">{{ __('talenma.talent.education_placeholder') }}</option>
                            @foreach ($educationOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('education_level', $profile->education_level) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('education_level')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="certifications" :value="__('talenma.talent.certifications')" />
                    <textarea id="certifications" name="certifications" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="{{ __('talenma.talent.certifications_placeholder') }}">{{ old('certifications', $profile->certifications) }}</textarea>
                    <x-input-error :messages="$errors->get('certifications')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="skills" :value="__('talenma.talent.skills')" />
                    <x-text-input id="skills" name="skills" class="mt-1 block w-full" :value="old('skills', is_array($profile->skills) ? implode(', ', $profile->skills) : '')" :placeholder="__('talenma.talent.skills_placeholder')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.talent.skills_hint') }}</p>
                    <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                </div>
            </section>

            <section class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_availability') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.section_availability_desc') }}</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="city" :value="__('talenma.talent.city')" />
                        <select id="city" name="city" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                            @foreach ($cities as $cityOption)
                                <option value="{{ $cityOption }}" @selected(old('city', $profile->city) === $cityOption)>{{ $cityOption }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="country" :value="__('talenma.talent.country')" />
                        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $profile->country ?? __('talenma.common.morocco'))" required />
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="availability" :value="__('talenma.talent.availability')" />
                        <select id="availability" name="availability" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            @foreach (['disponible' => 'available', 'sous 2 semaines' => 'two_weeks', 'mission en cours' => 'on_mission'] as $value => $key)
                                <option value="{{ $value }}" @selected(old('availability', $profile->availability) === $value)>{{ __('talenma.talent.'.$key) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('availability')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="daily_rate_eur" :value="__('talenma.talent.rate')" />
                        <x-text-input id="daily_rate_eur" name="daily_rate_eur" type="number" class="mt-1 block w-full" :value="old('daily_rate_eur', $profile->daily_rate_eur)" min="10" max="5000" required />
                        <x-input-error :messages="$errors->get('daily_rate_eur')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('talenma.talent.work_modes')" />
                    <div class="mt-3 grid sm:grid-cols-3 gap-3">
                        @php $selectedModes = old('work_modes', $profile->work_modes ?? []); @endphp
                        @foreach ($workModeOptions as $value => $label)
                            <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-indigo-200 cursor-pointer">
                                <input type="checkbox" name="work_modes[]" value="{{ $value }}" class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($value, $selectedModes, true))>
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('work_modes')" class="mt-2" />
                </div>

                <div>
                    <x-input-label :value="__('talenma.talent.languages')" />
                    <div class="mt-3 flex flex-wrap gap-3">
                        @php $selectedLanguages = old('languages', $profile->languages ?? []); @endphp
                        @foreach ($languageOptions as $value => $label)
                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm cursor-pointer hover:border-indigo-200">
                                <input type="checkbox" name="languages[]" value="{{ $value }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($value, $selectedLanguages, true))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('languages')" class="mt-2" />
                </div>
            </section>

            <section class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.links') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.links_desc') }}</p>
                </div>

                <div>
                    <x-input-label for="phone" :value="__('talenma.talent.phone')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $profile->phone)" placeholder="+212 6 00 00 00 00" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="linkedin_url" value="LinkedIn" />
                        <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="mt-1 block w-full" :value="old('linkedin_url', $profile->linkedin_url)" placeholder="https://linkedin.com/in/..." />
                        <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="github_url" value="GitHub" />
                        <x-text-input id="github_url" name="github_url" type="url" class="mt-1 block w-full" :value="old('github_url', $profile->github_url)" placeholder="https://github.com/..." />
                        <x-input-error :messages="$errors->get('github_url')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="portfolio_url" :value="__('talenma.talent.portfolio')" />
                        <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="mt-1 block w-full" :value="old('portfolio_url', $profile->portfolio_url)" placeholder="https://..." />
                        <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                    </div>
                </div>
            </section>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</a>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
