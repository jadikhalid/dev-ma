@php
    use App\Models\Profile;
    use App\Services\ProfessionCatalogService;
    use App\Services\ProfileDocumentService;

    $cvDocuments = $profile?->cvDocuments() ?? collect();
    $cvLanguageOptions = ProfileDocumentService::cvLanguageOptions();
    $workModeOptions = Profile::workModeOptions();
    $cvByLanguage = $cvDocuments->keyBy('language');
    $toneClass = match ($profile?->availability ? $profile->statusTone() : null) {
        'busy' => 'bg-slate-100 text-slate-700 ring-slate-200/80',
        'listening' => 'bg-amber-50 text-amber-800 ring-amber-200/80',
        'available', 'open' => 'bg-emerald-50 text-emerald-800 ring-emerald-200/80',
        default => $profile?->availability
            ? 'bg-emerald-50 text-emerald-800 ring-emerald-200/80'
            : null,
    };

    $professionCatalog = app(ProfessionCatalogService::class);
    $professionSectors = $professionCatalog->sectorsForLocale();
    $professionSlugs = $professionCatalog->slugsFromProfile(
        $profile?->profession_sector_id,
        $profile?->profession_id,
    );
    $sectorSlug = old('sector', $professionSlugs['sector'] ?? '');
    $professionSlug = old('profession', $professionSlugs['profession'] ?? '');
    $specializationValue = old('specialization', $profile?->specialization ?? '');
    $countryOptions = Profile::countryOptions();
    $citiesByCountry = Profile::citiesByCountry();

    $specialtyItems = collect(explode(',', (string) ($profile?->specialization ?? '')))
        ->map(fn ($item) => trim($item))
        ->filter()
        ->unique()
        ->values();
    $hasProfilData = filled($profile?->professionLabel())
        || filled($profile?->sectorLabel())
        || ($profile?->hasExperienceDeclared() ?? false)
        || $specialtyItems->isNotEmpty();
    $hasContact = filled($profile?->city)
        || filled($profile?->country)
        || filled($profile?->phone)
        || filled($profile?->whatsapp)
        || filled($profile?->linkedin_url)
        || filled($profile?->github_url)
        || filled($profile?->portfolio_url);
    $locationLine = collect([$profile?->city, $profile?->countryLabel()])->filter()->implode(', ');
    $contactRows = collect([
        $locationLine !== '' ? [
            'label' => __('talenma.talent.city'),
            'value' => $locationLine,
            'href' => null,
            'external' => false,
        ] : null,
        filled($profile?->phone) ? [
            'label' => __('talenma.talent.phone'),
            'value' => $profile->phone,
            'href' => 'tel:'.preg_replace('/\s+/', '', $profile->phone),
            'external' => false,
        ] : null,
        filled($profile?->whatsapp) ? [
            'label' => __('talenma.talent.whatsapp'),
            'value' => $profile->whatsapp,
            'href' => 'https://wa.me/'.preg_replace('/\D+/', '', $profile->whatsapp),
            'external' => true,
        ] : null,
        filled($profile?->linkedin_url) ? [
            'label' => 'LinkedIn',
            'value' => parse_url($profile->linkedin_url, PHP_URL_HOST) ?: $profile->linkedin_url,
            'href' => $profile->linkedin_url,
            'external' => true,
        ] : null,
        filled($profile?->github_url) ? [
            'label' => 'GitHub',
            'value' => parse_url($profile->github_url, PHP_URL_HOST) ?: $profile->github_url,
            'href' => $profile->github_url,
            'external' => true,
        ] : null,
        filled($profile?->portfolio_url) ? [
            'label' => __('talenma.talent.portfolio'),
            'value' => parse_url($profile->portfolio_url, PHP_URL_HOST) ?: $profile->portfolio_url,
            'href' => $profile->portfolio_url,
            'external' => true,
        ] : null,
    ])->filter()->values();
@endphp

<aside class="order-2 min-w-0 space-y-4 lg:order-none" data-talent-info-column>
    <div class="flex min-h-[3.25rem] items-center rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
        <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.profile_section_title') }}</h2>
    </div>

    <x-talent-video-snapshot
        class="!h-auto"
        :compact="true"
        :editable="true"
        :video-url="$profile->presentation_video_url ?? null"
        :person-name="trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->name"
    />

    {{-- CV --}}
    <section class="rounded-2xl border border-slate-200/90 bg-white p-3.5" data-dashboard-talent-cv>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.cv_card_title') }}</h3>
            <button
                type="button"
                class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                title="{{ __('talenma.dashboard.talent.cv_card_edit') }}"
                x-data
                x-on:click="$dispatch('open-modal', 'dashboard-talent-cv')"
            >
                <span class="sr-only">{{ __('talenma.dashboard.talent.cv_card_edit') }}</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
            </button>
        </div>

        <div class="mt-3 space-y-1.5">
            @forelse ($cvDocuments as $cvDocument)
                <a
                    href="{{ route('profile.documents.show', $cvDocument) }}"
                    target="_blank"
                    class="group flex items-center gap-1.5 rounded-lg border border-indigo-100/80 bg-indigo-50/50 px-2 py-1.5 transition hover:border-indigo-200 hover:bg-indigo-50"
                >
                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded bg-white text-indigo-600 ring-1 ring-indigo-100" aria-hidden="true">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1 truncate text-[11px] font-semibold text-indigo-800 group-hover:text-indigo-950" title="{{ $cvDocument->original_name }}">{{ $cvDocument->original_name }}</span>
                    <span class="shrink-0 text-[10px] font-medium text-indigo-500">{{ $cvDocument->languageLabel() }}</span>
                </a>
            @empty
                <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.cv_card_empty') }}</p>
            @endforelse
        </div>
    </section>

    <x-modal name="dashboard-talent-cv" maxWidth="lg" focusable>
        <div class="relative p-5 sm:p-6 space-y-5" id="dashboard-talent-cv-panel">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('talenma.dashboard.talent.cv_card_title') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.talent.cv_hint') }}</p>
                </div>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.common.cancel') }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2">
                @forelse ($cvDocuments as $document)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50/60 px-3 py-2.5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $document->original_name }}</p>
                            <p class="text-xs text-slate-500">{{ $document->languageLabel() }} · {{ $document->formattedSize() }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <a href="{{ route('profile.documents.show', $document) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.talent.document_view') }}</a>
                            <form
                                method="POST"
                                action="{{ route('profile.documents.destroy', $document) }}"
                                data-ajax
                                data-loading-target="dashboard-talent-cv-panel"
                                data-confirm="{{ __('talenma.talent.document_delete_confirm') }}"
                                data-error-message="{{ __('talenma.talent.save_error') }}"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="from_dashboard" value="1">
                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">{{ __('talenma.talent.document_remove') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('talenma.talent.cv_empty') }}</p>
                @endforelse
            </div>

            <form
                method="POST"
                action="{{ route('profile.details.update') }}"
                enctype="multipart/form-data"
                class="space-y-4 border-t border-slate-100 pt-4"
                data-ajax
                data-loading-target="dashboard-talent-cv-panel"
                data-error-message="{{ __('talenma.talent.save_error') }}"
                id="dashboard-talent-cv-form"
                novalidate
                x-data="talentDocumentsPicker({
                    maxBytes: {{ ProfileDocumentService::MAX_FILE_SIZE * 1024 }},
                    allowedMimes: @js(ProfileDocumentService::ALLOWED_MIMES),
                    existingCvs: @js($cvDocuments->map(fn ($document) => [
                        'language' => $document->language,
                        'name' => $document->original_name,
                    ])->values()),
                    messages: {
                        invalidType: @js(__('talenma.auth.validation.documents_type')),
                        tooLarge: @js(__('talenma.auth.validation.documents_size')),
                        duplicateName: @js(__('talenma.talent.cv_docs_duplicate')),
                    },
                })"
            >
                @csrf
                <input type="hidden" name="section" value="documents">
                <input type="hidden" name="from_dashboard" value="1">

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="dash_cv_language" :value="__('talenma.talent.cv_language')" />
                        <select
                            id="dash_cv_language"
                            name="cv_language"
                            x-ref="cvLanguage"
                            @change="onCvLanguageChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm"
                            required
                            data-required
                            data-required-message="{{ __('talenma.talent.cv_language_required') }}"
                        >
                            <option value="">{{ __('talenma.talent.cv_language_placeholder') }}</option>
                            @foreach ($cvLanguageOptions as $code => $label)
                                <option value="{{ $code }}">
                                    {{ $label }}@if ($cvByLanguage->has($code)) — {{ __('talenma.talent.cv_replace_hint') }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="dash_cv" :value="__('talenma.talent.cv_upload')" />
                        <input
                            id="dash_cv"
                            name="cv"
                            type="file"
                            x-ref="cvInput"
                            accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                            @change="onCvChange($event)"
                            required
                            data-required
                            data-required-message="{{ __('talenma.talent.cv_required') }}"
                            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <template x-if="pendingCv">
                            <div class="mt-2 flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900" x-text="pendingCv.name"></p>
                                    <p class="text-xs text-gray-500" x-text="formatSize(pendingCv.size)"></p>
                                </div>
                                <button type="button" class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700" @click="clearCv()">{{ __('talenma.talent.document_cancel_selection') }}</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" x-on:click="$dispatch('close')">{{ __('talenma.talent.cancel') }}</button>
                    <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Statut --}}
    <section class="rounded-2xl border border-slate-200/90 bg-white p-3.5" data-dashboard-talent-status>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.status_card_title') }}</h3>
            <button
                type="button"
                class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                title="{{ __('talenma.dashboard.talent.status_card_edit') }}"
                x-data
                x-on:click="$dispatch('open-modal', 'dashboard-talent-status')"
            >
                <span class="sr-only">{{ __('talenma.dashboard.talent.status_card_edit') }}</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
            </button>
        </div>

        <div class="mt-3 space-y-2.5">
            @if ($profile?->availability && $toneClass)
                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $toneClass }}">
                    {{ $profile->statusLabel() }}
                </span>

                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ __('talenma.talent.work_modes') }}</p>
                    @if ($profile->workModeLabels())
                        <div class="flex flex-wrap gap-1">
                            @foreach ($profile->workModeLabels() as $mode)
                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium leading-tight text-slate-700">{{ $mode }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.status_card_empty_modes') }}</p>
                    @endif
                </div>
            @else
                <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.status_card_empty') }}</p>
            @endif
        </div>
    </section>

    <x-modal name="dashboard-talent-status" maxWidth="lg" focusable>
        <form
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative space-y-5 p-5 sm:p-6"
            data-ajax
            data-loading-target="dashboard-talent-status-form"
            data-error-message="{{ __('talenma.talent.save_error') }}"
            id="dashboard-talent-status-form"
            novalidate
        >
            @csrf
            <input type="hidden" name="section" value="availability">
            <input type="hidden" name="from_dashboard" value="1">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('talenma.dashboard.talent.status_card_title') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.talent.section_availability_desc') }}</p>
                </div>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.common.cancel') }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <x-input-label for="dash_availability" :value="__('talenma.talent.status')" />
                <select
                    id="dash_availability"
                    name="availability"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm"
                    required
                    data-required
                    data-required-message="{{ __('talenma.talent.required_availability') }}"
                >
                    @foreach (Profile::statusOptions() as $value => $key)
                        <option value="{{ $value }}" @selected(old('availability', $profile?->availability ?? Profile::STATUS_AVAILABLE) === $value)>{{ __('talenma.talent.'.$key) }}</option>
                    @endforeach
                </select>
            </div>

            <div
                data-required-group
                data-required-message="{{ __('talenma.talent.required_work_modes') }}"
            >
                <x-input-label :value="__('talenma.talent.work_modes')" />
                <div class="mt-3 grid gap-2 sm:grid-cols-3">
                    @php $selectedModes = old('work_modes', $profile?->work_modes ?? []); @endphp
                    @foreach ($workModeOptions as $value => $label)
                        <label class="flex cursor-pointer items-start gap-2 rounded-xl border border-gray-200 p-3 hover:border-indigo-200">
                            <input type="checkbox" name="work_modes[]" value="{{ $value }}" class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($value, $selectedModes, true))>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" x-on:click="$dispatch('close')">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Profil --}}
    <section class="space-y-2.5 rounded-2xl border border-slate-200/90 bg-white p-3.5" data-dashboard-talent-profil>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.profile_snapshot') }}</h3>
            <button
                type="button"
                class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                title="{{ __('talenma.dashboard.talent.profil_card_edit') }}"
                x-data
                x-on:click="$dispatch('open-modal', 'dashboard-talent-profil')"
            >
                <span class="sr-only">{{ __('talenma.dashboard.talent.profil_card_edit') }}</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
            </button>
        </div>

        @if ($hasProfilData)
            <div class="space-y-1">
                @if ($profile?->professionLabel())
                    <h3 class="text-xs font-semibold leading-snug text-slate-900">{{ $profile->professionLabel() }}</h3>
                @else
                    <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.profil_card_empty_profession') }}</p>
                @endif

                @if ($profile?->sectorLabel() || ($profile?->hasExperienceDeclared() ?? false))
                    <p class="text-[11px] leading-snug text-slate-600">
                        @if ($profile?->sectorLabel())
                            <span class="font-medium text-indigo-700">{{ $profile->sectorLabel() }}</span>
                        @endif
                        @if ($profile?->sectorLabel() && ($profile?->hasExperienceDeclared() ?? false))
                            <span class="text-slate-300"> · </span>
                        @endif
                        @if ($profile?->hasExperienceDeclared())
                            <span>{{ $profile->experienceLabel() }}</span>
                        @endif
                    </p>
                @endif
            </div>

            <div class="space-y-1 border-t border-slate-100 pt-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400">{{ __('talenma.dashboard.talent.specialty_skills') }}</p>
                @if ($specialtyItems->isNotEmpty())
                    <div class="flex flex-wrap gap-1">
                        @foreach ($specialtyItems as $item)
                            <span class="rounded bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium leading-tight text-indigo-800">{{ $item }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.profil_card_empty_specialty') }}</p>
                @endif
            </div>
        @else
            <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.profil_card_empty') }}</p>
        @endif
    </section>

    <x-modal name="dashboard-talent-profil" maxWidth="2xl" focusable>
        <form
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative space-y-5 p-5 sm:p-6"
            data-ajax
            data-loading-target="dashboard-talent-profil-form"
            data-error-message="{{ __('talenma.talent.save_error') }}"
            id="dashboard-talent-profil-form"
            novalidate
        >
            @csrf
            <input type="hidden" name="section" value="profession">
            <input type="hidden" name="from_dashboard" value="1">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('talenma.dashboard.talent.profile_snapshot') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.talent.section_profession') }}</p>
                </div>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.common.cancel') }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <x-profile-profession-fields
                :sectors="$professionSectors"
                :sector="$sectorSlug"
                :profession="$professionSlug"
                :specialization="$specializationValue"
            />

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" x-on:click="$dispatch('close')">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Coordonnées --}}
    <section class="space-y-2 rounded-2xl border border-slate-200/90 bg-white p-3.5" data-dashboard-talent-contact>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.dashboard.talent.contact_snapshot') }}</h3>
            <button
                type="button"
                class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                title="{{ __('talenma.dashboard.talent.contact_card_edit') }}"
                x-data
                x-on:click="$dispatch('open-modal', 'dashboard-talent-contact')"
            >
                <span class="sr-only">{{ __('talenma.dashboard.talent.contact_card_edit') }}</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
            </button>
        </div>

        @if ($hasContact)
            <dl class="space-y-1">
                @foreach ($contactRows as $row)
                    <div class="flex items-baseline justify-between gap-2">
                        <dt class="shrink-0 text-[10px] font-bold uppercase tracking-[0.06em] text-slate-400">{{ $row['label'] }}</dt>
                        <dd class="min-w-0 max-w-[65%] text-right text-[11px] font-semibold leading-snug text-slate-900">
                            @if ($row['href'])
                                <a
                                    href="{{ $row['href'] }}"
                                    @if ($row['external']) target="_blank" rel="noopener noreferrer" @endif
                                    class="block truncate text-indigo-700 transition hover:text-indigo-900"
                                    title="{{ $row['value'] }}"
                                >{{ $row['value'] }}</a>
                            @else
                                <span class="block truncate">{{ $row['value'] }}</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        @else
            <p class="text-[11px] leading-snug text-slate-500">{{ __('talenma.dashboard.talent.contact_empty') }}</p>
        @endif
    </section>

    <x-modal name="dashboard-talent-contact" maxWidth="2xl" focusable>
        <form
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative space-y-5 p-5 sm:p-6"
            data-ajax
            data-loading-target="dashboard-talent-contact-form"
            data-error-message="{{ __('talenma.talent.save_error') }}"
            id="dashboard-talent-contact-form"
            novalidate
            x-data="talentLocationSelect({
                country: @js(old('country', $profile?->country)),
                city: @js(old('city', $profile?->city)),
                citiesByCountry: @js($citiesByCountry),
            })"
        >
            @csrf
            <input type="hidden" name="section" value="links">
            <input type="hidden" name="from_dashboard" value="1">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ __('talenma.dashboard.talent.contact_snapshot') }}</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.talent.links') }}</p>
                </div>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.common.cancel') }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="dash_country" :value="__('talenma.talent.country')" />
                    <select
                        id="dash_country"
                        name="country"
                        x-model="country"
                        @change="onCountryChange()"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm"
                    >
                        <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                        @foreach ($countryOptions as $code => $label)
                            <option value="{{ $code }}" @selected(old('country', $profile?->country) === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="dash_city" :value="__('talenma.talent.city')" />
                    <input type="hidden" name="city" :value="city">
                    <select
                        id="dash_city"
                        x-model="city"
                        :disabled="!country"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm disabled:bg-gray-50 disabled:text-gray-400"
                    >
                        <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                        @foreach ($citiesByCountry as $countryCode => $cityList)
                            @foreach ($cityList as $cityOption)
                                <option
                                    value="{{ $cityOption }}"
                                    data-country="{{ $countryCode }}"
                                    @selected(old('city', $profile?->city) === $cityOption && old('country', $profile?->country) === $countryCode)
                                    :hidden="country !== '{{ $countryCode }}'"
                                    :disabled="country !== '{{ $countryCode }}'"
                                >{{ $cityOption }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="dash_phone" :value="__('talenma.talent.phone')" />
                    <x-text-input
                        id="dash_phone"
                        name="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        :value="old('phone', $profile?->phone)"
                        placeholder="+212 6 00 00 00 00"
                        maxlength="30"
                        data-phone
                        data-phone-message="{{ __('talenma.talent.phone_invalid') }}"
                    />
                </div>
                <div>
                    <x-input-label for="dash_whatsapp" :value="__('talenma.talent.whatsapp')" />
                    <x-text-input
                        id="dash_whatsapp"
                        name="whatsapp"
                        type="tel"
                        class="mt-1 block w-full"
                        :value="old('whatsapp', $profile?->whatsapp)"
                        placeholder="+212 6 00 00 00 00"
                        maxlength="30"
                        data-phone
                        data-phone-message="{{ __('talenma.talent.whatsapp_invalid') }}"
                    />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="dash_linkedin_url" value="LinkedIn" />
                    <x-text-input
                        id="dash_linkedin_url"
                        name="linkedin_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('linkedin_url', $profile?->linkedin_url)"
                        placeholder="https://linkedin.com/in/..."
                        data-url
                        data-url-message="{{ __('talenma.talent.linkedin_invalid') }}"
                        data-url-host="linkedin.com"
                        data-url-host-message="{{ __('talenma.talent.linkedin_host_invalid') }}"
                    />
                </div>
                <div>
                    <x-input-label for="dash_github_url" value="GitHub" />
                    <x-text-input
                        id="dash_github_url"
                        name="github_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('github_url', $profile?->github_url)"
                        placeholder="https://github.com/..."
                        data-url
                        data-url-message="{{ __('talenma.talent.github_invalid') }}"
                        data-url-host="github.com"
                        data-url-host-message="{{ __('talenma.talent.github_host_invalid') }}"
                    />
                </div>
                <div>
                    <x-input-label for="dash_portfolio_url" :value="__('talenma.talent.portfolio')" />
                    <x-text-input
                        id="dash_portfolio_url"
                        name="portfolio_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('portfolio_url', $profile?->portfolio_url)"
                        placeholder="https://..."
                        data-url
                        data-url-message="{{ __('talenma.talent.portfolio_invalid') }}"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" x-on:click="$dispatch('close')">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>
    </x-modal>
</aside>
