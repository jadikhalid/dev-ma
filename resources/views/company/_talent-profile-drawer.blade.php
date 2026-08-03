{{-- Requires Alpine parent: companyTalentProfileDrawer / companyTalentCatalog --}}
<div
    x-show="profileDrawerOpen"
    x-cloak
    class="fixed inset-0 z-[70] h-screen"
    style="margin: 0; height: 100vh; min-height: 100vh; max-height: 100vh;"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('talenma.talent.profile_title') }}"
    @keydown.escape.window="onProfileEscape()"
>
    <div
        x-show="profileDrawerOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/45"
        @click="closeProfile()"
    ></div>

    <aside
        x-show="profileDrawerOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 bottom-0 flex h-screen min-h-screen max-h-screen w-full max-w-2xl flex-col bg-white shadow-2xl"
        style="height: 100vh; min-height: 100vh; max-height: 100vh;"
        @click.stop
    >
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                {{ __('talenma.talent.profile_title') }}
            </p>
            <button
                type="button"
                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                aria-label="{{ __('talenma.common.close') }}"
                @click="closeProfile()"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-7">
            <div x-show="profileLoading" class="flex items-center justify-center gap-3 py-20 text-sm text-gray-500">
                <svg class="h-5 w-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 3.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ __('talenma.home.search_drawer_loading') }}</span>
            </div>

            <div
                x-show="!profileLoading && profileError"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                x-text="profileError"
            ></div>

            <template x-if="!profileLoading && selectedProfile">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-2xl font-bold text-gray-900" x-text="selectedProfile.name"></h2>
                            <p class="mt-1 text-sm font-medium text-indigo-600">
                                <span x-text="selectedProfile.profession_label"></span>
                                <span x-show="selectedProfile.profession_label && selectedProfile.sector_label"> - </span>
                                <span x-text="selectedProfile.sector_label"></span>
                            </p>
                            <p
                                class="mt-1 text-sm text-gray-500"
                                x-show="selectedProfile.employer_label"
                                x-text="'{{ __('talenma.talent.employer') }} : ' + selectedProfile.employer_label"
                            ></p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-2">
                            <template x-if="selectedProfile.avatar_url">
                                <img
                                    :src="selectedProfile.avatar_url"
                                    :alt="selectedProfile.name"
                                    class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200"
                                >
                            </template>
                            <template x-if="!selectedProfile.avatar_url">
                                <span
                                    class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-700"
                                    x-text="selectedProfile.initials"
                                    aria-hidden="true"
                                ></span>
                            </template>
                            <span
                                x-show="selectedProfile.availability_label"
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="profileStatusClass(selectedProfile.availability_tone)"
                                x-text="selectedProfile.availability_label"
                            ></span>
                            <span
                                x-show="selectedProfile.talent_locked"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-900 ring-1 ring-amber-200"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                {{ __('talenma.recruitment.talent_lock_badge') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2 text-sm">
                        <span
                            x-show="selectedProfile.experience_label"
                            class="rounded-lg bg-emerald-50 px-3 py-1.5 font-bold text-emerald-700"
                            x-text="selectedProfile.experience_label"
                        ></span>
                    </div>

                    <section x-show="selectedProfile.keywords?.length" class="mt-7">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.talent.specialty_skills') }}</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <template x-for="keyword in selectedProfile.keywords" :key="keyword">
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700" x-text="keyword"></span>
                            </template>
                        </div>
                    </section>

                    <section
                        x-show="selectedProfile.work_modes?.length || selectedProfile.languages?.length"
                        class="mt-7 grid gap-5 sm:grid-cols-2"
                    >
                        <div x-show="selectedProfile.work_modes?.length">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.work_modes') }}</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="mode in selectedProfile.work_modes" :key="mode">
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700" x-text="mode"></span>
                                </template>
                            </div>
                        </div>
                        <div x-show="selectedProfile.languages?.length">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.languages') }}</h3>
                            <p class="mt-2 text-sm text-gray-600" x-text="selectedProfile.languages.join(', ')"></p>
                        </div>
                    </section>

                    <section
                        x-show="selectedProfile.education_label || selectedProfile.certifications"
                        class="mt-7 grid gap-5 sm:grid-cols-2"
                    >
                        <div x-show="selectedProfile.education_label">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.education') }}</h3>
                            <p class="mt-2 text-sm text-gray-600" x-text="selectedProfile.education_label"></p>
                        </div>
                        <div x-show="selectedProfile.certifications">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.certifications') }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm text-gray-600" x-text="selectedProfile.certifications"></p>
                        </div>
                    </section>

                    <section x-show="selectedProfile.bio" class="mt-7">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.talents.presentation') }}</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700" x-text="selectedProfile.bio"></p>
                    </section>

                    <div class="mt-8 flex flex-wrap gap-3 border-t pt-6">
                        <a
                            x-show="selectedProfile.direct_hire_url && selectedProfile.can_propose_direct_hire !== false"
                            :href="selectedProfile.direct_hire_url"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                        >{{ __('talenma.direct_hire.cta_btn') }}</a>
                        <span
                            x-show="selectedProfile.direct_hire_url && selectedProfile.can_propose_direct_hire === false"
                            class="inline-flex cursor-not-allowed rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-400"
                            :title="selectedProfile.direct_hire_disabled_hint || labels.directHireDisabled"
                        >{{ __('talenma.direct_hire.cta_btn') }}</span>
                        <button
                            type="button"
                            x-show="selectedProfile.direct_hire_unlock_url"
                            x-cloak
                            :disabled="unlockSending"
                            @click="unlockTalent(selectedProfile.direct_hire_unlock_url)"
                            class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100 disabled:opacity-60"
                        >{{ __('talenma.direct_hire.talent_unlock_btn') }}</button>
                        <a
                            x-show="selectedProfile.recruitment_url && selectedProfile.can_request_named !== false"
                            :href="selectedProfile.recruitment_url"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        >{{ __('talenma.talents.inter_btn') }}</a>
                        <span
                            x-show="selectedProfile.can_request_named === false"
                            class="inline-flex cursor-not-allowed rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-400"
                            :title="selectedProfile.named_request_disabled_hint || labels.namedDisabled || '{{ __('talenma.recruitment.named_blocked_open') }}'"
                        >{{ __('talenma.talents.inter_btn') }}</span>
                        <button
                            type="button"
                            x-show="selectedProfile.named_unlock_url"
                            x-cloak
                            :disabled="unlockSending"
                            @click="unlockTalent(selectedProfile.named_unlock_url)"
                            class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100 disabled:opacity-60"
                        >{{ __('talenma.recruitment.talent_unlock_btn') }}</button>
                        <a
                            :href="selectedProfile.cv_url || '#'"
                            :target="selectedProfile.cv_url ? '_blank' : null"
                            :rel="selectedProfile.cv_url ? 'noopener' : null"
                            :aria-disabled="!selectedProfile.cv_url"
                            :tabindex="selectedProfile.cv_url ? 0 : -1"
                            @click="if (! selectedProfile.cv_url) { $event.preventDefault() }"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold"
                            :class="selectedProfile.cv_url
                                ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                : 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none'"
                        >{{ __('talenma.talents.view_cv') }}</a>
                        <button
                            type="button"
                            :disabled="!selectedProfile.presentation_video_url"
                            @click="openVideoModal(selectedProfile)"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold"
                            :class="selectedProfile.presentation_video_url
                                ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                : 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'"
                        >{{ __('talenma.talents.view_video') }}</button>
                        <a
                            x-show="selectedProfile.linkedin_url"
                            :href="selectedProfile.linkedin_url"
                            target="_blank"
                            rel="noopener"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >LinkedIn</a>
                        <a
                            x-show="selectedProfile.github_url"
                            :href="selectedProfile.github_url"
                            target="_blank"
                            rel="noopener"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >GitHub</a>
                        <a
                            x-show="selectedProfile.portfolio_url"
                            :href="selectedProfile.portfolio_url"
                            target="_blank"
                            rel="noopener"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >Portfolio</a>
                    </div>

                    <div
                        x-show="composeUrl"
                        class="mt-7 relative rounded-xl border bg-gray-50 p-5 space-y-4"
                    >
                        <div
                            x-show="composeSending"
                            x-cloak
                            class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]"
                            aria-hidden="true"
                        >
                            <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-5 py-4 shadow-sm ring-1 ring-gray-200">
                                <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-900">{{ __('talenma.inbox.compose_title') }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ __('talenma.inbox.compose_desc') }}</p>
                        </div>

                        <template x-if="composeSuccessUrl">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 space-y-2">
                                <p>{{ __('talenma.inbox.compose_success') }}</p>
                                <a :href="composeSuccessUrl" class="inline-flex font-semibold text-emerald-900 underline">
                                    {{ __('talenma.inbox.compose_open_thread') }}
                                </a>
                            </div>
                        </template>

                        <form x-show="!composeSuccessUrl" class="space-y-3" @submit.prevent="sendCompose()">
                            <div>
                                <label class="block text-xs font-medium text-gray-600" for="compose-subject-drawer">{{ __('talenma.inbox.compose_subject') }}</label>
                                <input
                                    id="compose-subject-drawer"
                                    type="text"
                                    x-model="composeSubject"
                                    maxlength="255"
                                    required
                                    :disabled="composeSending"
                                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                    placeholder="{{ __('talenma.inbox.compose_subject_placeholder') }}"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600" for="compose-body-drawer">{{ __('talenma.inbox.compose_body') }}</label>
                                <textarea
                                    id="compose-body-drawer"
                                    x-model="composeBody"
                                    rows="5"
                                    required
                                    minlength="20"
                                    maxlength="5000"
                                    :disabled="composeSending"
                                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                    placeholder="{{ __('talenma.inbox.compose_body_placeholder') }}"
                                ></textarea>
                            </div>
                            <div>
                                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600" :class="composeSending && 'pointer-events-none opacity-60'">
                                    <input type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" @change="onComposeFiles($event)" :disabled="composeSending">
                                    <span class="rounded-lg border bg-white px-3 py-1.5 hover:bg-gray-50">{{ __('talenma.inbox.attach') }}</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-400">{{ __('talenma.inbox.attachments_hint') }}</p>
                                <ul class="mt-1 space-y-1 text-xs text-gray-600" x-show="composeFiles.length">
                                    <template x-for="(file, index) in composeFiles" :key="file.name + index">
                                        <li class="flex items-center gap-2">
                                            <span x-text="file.name"></span>
                                            <button type="button" class="text-red-600" @click="removeComposeFile(index)" :disabled="composeSending">×</button>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <p x-show="composeError" class="text-sm text-red-600" x-text="composeError"></p>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="submit"
                                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                                    :disabled="composeSending"
                                    x-text="composeSending ? @js(__('talenma.inbox.sending')) : @js(__('talenma.inbox.compose_send'))"
                                ></button>
                                <button
                                    type="button"
                                    class="rounded-lg border bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                    @click="resetCompose()"
                                    :disabled="composeSending"
                                >{{ __('talenma.inbox.compose_cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </aside>
</div>

<div
    x-show="videoModalOpen"
    x-cloak
    class="fixed inset-0 z-[90] flex items-center justify-center p-4 sm:p-8"
    style="margin: 0; height: 100vh; min-height: 100vh; max-height: 100vh;"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('talenma.talents.view_video') }}"
>
    <div
        class="absolute inset-0 bg-gray-950/70"
        style="height: 100vh; min-height: 100vh; max-height: 100vh;"
        @click="closeVideoModal()"
    ></div>
    <div
        class="relative z-10 w-full max-w-3xl overflow-hidden rounded-2xl bg-black shadow-2xl"
        @click.stop
    >
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
            <p class="truncate text-sm font-semibold text-white">
                <span x-text="videoModalTalent?.name"></span>
                — {{ __('talenma.talents.view_video') }}
            </p>
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-2xl font-semibold leading-none text-white/80 hover:bg-white/10 hover:text-white"
                @click="closeVideoModal()"
                aria-label="{{ __('talenma.talents.close_video') }}"
            >×</button>
        </div>
        <div class="aspect-video bg-black">
            <template x-if="videoModalOpen && videoModalTalent?.presentation_video_url">
                <video
                    x-ref="profileVideoPlayer"
                    class="h-full w-full"
                    controls
                    playsinline
                    autoplay
                    :src="videoModalTalent.presentation_video_url"
                ></video>
            </template>
        </div>
    </div>
</div>
