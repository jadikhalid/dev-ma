@php
    /** @var \App\Models\CompanyProfile|null $profile */
    $canEditOrg = $canEditOrg ?? false;
    $owner = $profile?->user;
    $members = $profile?->memberships ?? collect();
    $location = collect([$profile?->city, $profile?->countryLabel()])->filter()->implode(', ');
    $companyIdentityPanel = $companyIdentityPanel ?? null;

    $ficheRows = collect([
        ['label' => __('talenma.company.sector'), 'value' => $profile?->sector],
        ['label' => __('talenma.company.employees'), 'value' => $profile?->employee_count],
        ['label' => __('talenma.company.check_location'), 'value' => $location !== '' ? $location : null],
        ['label' => __('talenma.company.website'), 'value' => $profile?->website],
    ])->filter(fn (array $row) => filled($row['value']))->values();
@endphp

<aside class="min-w-0 space-y-3" data-company-org-column>
    <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </span>
            <h2 class="text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.dashboard.company.my_company') }}</h2>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5" data-dashboard-contact-card>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company.section_contact') }}</h3>
            @if ($canEditOrg)
                <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                    title="{{ __('talenma.dashboard.company.invite_change') }}"
                    x-data
                    x-on:click="$dispatch('open-modal', 'dashboard-company-contact')"
                >
                    <span class="sr-only">{{ __('talenma.dashboard.company.invite_change') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                </button>
            @endif
        </div>
        <div class="mt-3 flex items-start gap-2.5">
            <x-company-contact-photo :profile="$profile" size="sm" class="shrink-0" />
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-900" data-dashboard-contact-name>{{ $profile?->representative_name ?: '—' }}</p>
                <p class="truncate text-xs text-slate-500">{{ $owner?->email ?: '—' }}</p>
                <p class="truncate text-xs text-slate-500" data-dashboard-contact-phone>{{ $profile?->phone ?: '—' }}</p>
            </div>
        </div>
    </section>

    @if ($canEditOrg)
        <x-modal name="dashboard-company-contact" maxWidth="lg" focusable>
            <form
                method="post"
                action="{{ route('profile.contact.update') }}"
                enctype="multipart/form-data"
                class="relative p-5 sm:p-6 space-y-5"
                data-ajax
                data-loading-target="dashboard-company-contact-form"
                data-error-message="{{ __('talenma.common.save_error') }}"
                id="dashboard-company-contact-form"
                novalidate
                x-data="avatarPreview({
                    initialUrl: @js($profile?->representativePhotoUrl()),
                    initials: @js($profile?->representativeInitials() ?? '—'),
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
                <input type="hidden" name="from_dashboard" value="1">

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">{{ __('talenma.company.section_contact') }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ __('talenma.company.section_contact_desc') }}</p>
                        @if ($owner?->email)
                            <p class="mt-1 text-[11px] text-slate-400">{{ __('talenma.company.section_contact_email_hint', ['email' => $owner->email]) }}</p>
                        @endif
                    </div>
                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.common.cancel') }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-4 border-b border-slate-100">
                    <div class="relative shrink-0">
                        <img
                            x-show="previewUrl"
                            x-cloak
                            :src="previewUrl"
                            alt="{{ $profile?->representative_name ?: __('talenma.company.section_contact') }}"
                            class="h-20 w-20 rounded-full object-cover ring-2 ring-indigo-100"
                        >
                        <span
                            x-show="!previewUrl"
                            class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-700"
                            aria-hidden="true"
                            x-text="initials"
                        ></span>
                        <span
                            x-show="processing"
                            x-cloak
                            class="absolute inset-0 flex items-center justify-center rounded-full bg-white/70 text-xs font-semibold text-indigo-700"
                        >…</span>
                    </div>
                    <div class="min-w-0 flex-1 space-y-2">
                        <div>
                            <x-input-label for="dash_representative_photo" :value="__('talenma.company.representative_photo')" />
                            <input
                                id="dash_representative_photo"
                                name="representative_photo"
                                type="file"
                                x-ref="input"
                                accept="image/jpeg,image/png,image/webp"
                                @change="onFileChange($event)"
                                class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                            >
                            <p class="mt-1 text-[11px] text-slate-500">{{ __('talenma.company.representative_photo_hint') }}</p>
                        </div>
                        @if ($profile?->representative_photo_path)
                            <label class="inline-flex items-center gap-2 text-xs text-slate-600">
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
                    <x-input-label for="dash_representative_name" :value="__('talenma.company.contact_full_name')" />
                    <x-text-input
                        id="dash_representative_name"
                        name="representative_name"
                        class="mt-1 block w-full"
                        :value="old('representative_name', $profile?->representative_name)"
                        required
                        data-required
                        data-required-message="{{ __('talenma.company.representative_name_required') }}"
                    />
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
                            placeholder="+33 6 00 00 00 00"
                            data-phone
                            data-phone-message="{{ __('talenma.company.phone_invalid') }}"
                        />
                    </div>
                    <div>
                        <x-input-label for="dash_linkedin_url" :value="__('talenma.company.linkedin')" />
                        <x-text-input
                            id="dash_linkedin_url"
                            name="linkedin_url"
                            type="url"
                            class="mt-1 block w-full"
                            :value="old('linkedin_url', $profile?->linkedin_url)"
                            placeholder="https://linkedin.com/in/..."
                            data-url
                            data-url-message="{{ __('talenma.company.linkedin_invalid') }}"
                            data-url-host="linkedin.com"
                            data-url-host-message="{{ __('talenma.company.linkedin_host') }}"
                        />
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 pt-1 sm:flex-row sm:justify-end">
                    <button type="button" data-reset class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close')">
                        {{ __('talenma.common.cancel') }}
                    </button>
                    <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5" data-dashboard-users-card>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company_users.title') }}</h3>
            @if ($canEditOrg)
                <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                    title="{{ __('talenma.dashboard.company.invite_change') }}"
                    x-data
                    x-on:click="$dispatch('open-modal', 'dashboard-company-users')"
                >
                    <span class="sr-only">{{ __('talenma.dashboard.company.invite_change') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                </button>
            @endif
        </div>
        @if ($members->isEmpty())
            <p class="mt-3 text-xs text-slate-500">{{ __('talenma.company_users.empty') }}</p>
        @else
            <ul class="mt-3 space-y-2">
                @foreach ($members->take(4) as $membership)
                    @php
                        $member = $membership->user;
                        $memberName = trim((string) (($member?->first_name ?? '').' '.($member?->last_name ?? '')));
                    @endphp
                    <li class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $memberName !== '' ? $memberName : ($member?->name ?: '—') }}</p>
                        @if (filled($membership->job_title))
                            <p class="truncate text-xs text-indigo-700">{{ $membership->job_title }}</p>
                        @endif
                        <p class="truncate text-xs text-slate-500">{{ $member?->email }}</p>
                    </li>
                @endforeach
            </ul>
            @if ($members->count() > 4)
                <p class="mt-2 text-[11px] font-semibold text-indigo-700">+{{ $members->count() - 4 }}</p>
            @endif
        @endif
    </section>

    @if ($canEditOrg)
        <x-modal name="dashboard-company-users" maxWidth="2xl" focusable>
            @include('profile.partials.company-users-panel', [
                'memberships' => $members,
                'usersCardId' => 'dashboard-company-users-card',
                'idPrefix' => 'dash_',
                'fromDashboard' => true,
                'variant' => 'modal',
            ])
        </x-modal>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5" data-dashboard-identity-card>
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company.section_identity') }}</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">{{ __('talenma.company.section_identity_desc') }}</p>
            </div>
            @if ($canEditOrg && $companyIdentityPanel && $profile)
                <button
                    type="button"
                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white"
                    title="{{ __('talenma.dashboard.company.invite_change') }}"
                    x-data
                    x-on:click="$dispatch('open-modal', 'dashboard-company-identity')"
                >
                    <span class="sr-only">{{ __('talenma.dashboard.company.invite_change') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                </button>
            @endif
        </div>
        @if ($ficheRows->isNotEmpty())
            <ul class="mt-3 space-y-2.5">
                @foreach ($ficheRows as $row)
                    <li class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $row['label'] }}</p>
                        <p class="truncate text-xs font-medium text-slate-900" title="{{ $row['value'] }}">{{ $row['value'] }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($canEditOrg && $companyIdentityPanel && $profile)
        <x-modal name="dashboard-company-identity" maxWidth="2xl" focusable>
            @include('company.partials.identity-form', array_merge($companyIdentityPanel, [
                'formId' => 'dashboard-company-identity-card',
                'idPrefix' => 'dash_',
                'fromDashboard' => true,
                'variant' => 'modal',
            ]))
        </x-modal>
    @endif
</aside>
