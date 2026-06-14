<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.talent.profile_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.talent.profile_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6">
        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.talent.updated') }}</div>
        @endif

        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <form method="POST" action="{{ route('profile.details.update') }}" class="space-y-6">@csrf
                <div>
                    <x-input-label for="title" :value="__('talenma.talent.title')" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $profile->title)" placeholder="Talent Full Stack Laravel & React" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="city" :value="__('talenma.talent.city')" />
                        <x-text-input id="city" name="city" class="mt-1 block w-full" :value="old('city', $profile->city)" required />
                    </div>
                    <div>
                        <x-input-label for="country" :value="__('talenma.talent.country')" />
                        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $profile->country ?? __('talenma.common.morocco'))" required />
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="experience_years" :value="__('talenma.talent.experience')" />
                        <x-text-input id="experience_years" name="experience_years" type="number" class="mt-1 block w-full" :value="old('experience_years', $profile->experience_years)" min="0" required />
                    </div>
                    <div>
                        <x-input-label for="daily_rate_eur" :value="__('talenma.talent.rate')" />
                        <x-text-input id="daily_rate_eur" name="daily_rate_eur" type="number" class="mt-1 block w-full" :value="old('daily_rate_eur', $profile->daily_rate_eur)" min="10" required />
                    </div>
                    <div>
                        <x-input-label for="availability" :value="__('talenma.talent.availability')" />
                        <select id="availability" name="availability" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            @foreach (['disponible' => 'available', 'sous 2 semaines' => 'two_weeks', 'mission en cours' => 'on_mission'] as $value => $key)
                                <option value="{{ $value }}" {{ old('availability', $profile->availability) === $value ? 'selected' : '' }}>{{ __('talenma.talent.'.$key) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label for="skills" :value="__('talenma.talent.skills')" />
                    <x-text-input id="skills" name="skills" class="mt-1 block w-full" :value="old('skills', is_array($profile->skills) ? implode(', ', $profile->skills) : '')" placeholder="Laravel, React, Node.js, Docker" />
                </div>

                <div>
                    <x-input-label for="bio" :value="__('talenma.talent.bio')" />
                    <textarea id="bio" name="bio" rows="5" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" required>{{ old('bio', $profile->bio) }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="linkedin_url" value="LinkedIn" />
                        <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="mt-1 block w-full" :value="old('linkedin_url', $profile->linkedin_url)" />
                    </div>
                    <div>
                        <x-input-label for="github_url" value="GitHub" />
                        <x-text-input id="github_url" name="github_url" type="url" class="mt-1 block w-full" :value="old('github_url', $profile->github_url)" />
                    </div>
                    <div>
                        <x-input-label for="portfolio_url" value="Portfolio" />
                        <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="mt-1 block w-full" :value="old('portfolio_url', $profile->portfolio_url)" />
                    </div>
                </div>

                <x-primary-button>{{ __('talenma.talent.save') }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
