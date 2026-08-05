<div
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
    :aria-label="labels.drawerTitle"
    @keydown.escape.window="close()"
>
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/40"
        @click="close()"
    ></div>

    <div
        x-show="open"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 right-0 w-full sm:w-1/2 bg-white shadow-2xl flex flex-col"
        @click.stop
    >
        <div class="shrink-0 flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-100">
            <div class="min-w-0 flex items-start gap-3">
                <template x-if="user?.role === 'company' && user?.current_profile?.logo_url">
                    <img
                        :src="user.current_profile.logo_url"
                        :alt="user.name"
                        class="shrink-0 w-12 h-12 rounded-xl object-cover border border-gray-200 bg-white"
                    >
                </template>
                <template x-if="user?.role !== 'company' && user?.current_profile?.photo_url">
                    <img
                        :src="user.current_profile.photo_url"
                        :alt="user.name"
                        class="shrink-0 w-12 h-12 rounded-full object-cover border border-gray-200 bg-white"
                    >
                </template>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.admin.users.registration_drawer_title') }}</p>
                    <h3 class="mt-1 text-lg font-bold text-gray-900 truncate" x-text="user?.name ?? '…'"></h3>
                    <p class="text-sm text-gray-500 truncate" x-text="user?.email ?? ''"></p>
                </div>
            </div>
            <button
                type="button"
                @click="close()"
                class="shrink-0 p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                :aria-label="labels.close"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5">
            <div x-show="loading" class="flex items-center justify-center py-16 text-sm text-gray-500">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                …
            </div>

            <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" x-text="error"></div>

            <template x-if="user && !loading">
                <div class="space-y-6">
                    <section>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('talenma.admin.users.registration_section_identity') }}</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="rounded-xl bg-gray-50 px-4 py-3" x-show="user.first_name">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.first_name') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.first_name"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3" x-show="user.last_name">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.last_name') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.last_name"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.full_name') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.name"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.email') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900 break-all" x-text="user.email"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.registration_registered_at') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.registered_at"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.registration_email_verified') }}</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="user.email_verified ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'"
                                        x-text="user.email_verified ? labels.emailVerified : labels.emailUnverified"
                                    ></span>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('talenma.admin.users.registration_section_approval') }}</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.registration_approval_status') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.approval_status_label"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3" x-show="user.approved_at">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.registration_approved_at') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.approved_at"></dd>
                            </div>
                            <div class="rounded-xl bg-gray-50 px-4 py-3 sm:col-span-2" x-show="user.approved_by">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.registration_approved_by') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.approved_by"></dd>
                            </div>
                        </dl>
                    </section>

                    <section>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('talenma.admin.users.registration_section_documents') }}</h4>
                        <template x-if="user.documents.length === 0">
                            <p class="text-sm text-gray-500 rounded-xl bg-gray-50 px-4 py-3">{{ __('talenma.admin.users.registration_no_documents') }}</p>
                        </template>
                        <div class="grid gap-3 sm:grid-cols-2" x-show="user.documents.length > 0">
                            <template x-for="document in user.documents" :key="document.id">
                                <a
                                    :href="document.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex gap-3 rounded-xl border border-gray-200 p-3 hover:border-indigo-300 hover:bg-indigo-50/40 transition"
                                >
                                    <div class="shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                        <img x-show="document.is_image" :src="document.url" :alt="document.name" class="w-full h-full object-cover">
                                        <svg x-show="!document.is_image" x-cloak class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a2.25 2.25 0 0 0-2.25-2.25h-4.125A2.25 2.25 0 0 0 9.75 9.75V7.5m6 0V4.875A2.25 2.25 0 0 0 13.5 2.625h-2.25A2.25 2.25 0 0 0 9 4.875V7.5m0 0h6M9 18.75h6A2.25 2.25 0 0 0 17.25 16.5v-1.5H6.75v1.5A2.25 2.25 0 0 0 9 18.75Z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-700" x-text="document.name"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="document.size"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </section>

                    <section x-show="user.role !== 'company'">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('talenma.admin.users.registration_section_current_profile') }}</h4>
                        <template x-if="!user.current_profile || Object.keys(user.current_profile).length === 0">
                            <p class="text-sm text-gray-500 rounded-xl bg-gray-50 px-4 py-3" x-text="labels.currentProfileEmpty"></p>
                        </template>
                        <dl
                            x-show="user.current_profile && Object.keys(user.current_profile).length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"
                        >
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.photo_url">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_photo') }}</dt>
                                <dd class="mt-2">
                                    <img
                                        :src="user.current_profile.photo_url"
                                        :alt="user.name"
                                        class="w-20 h-20 rounded-full object-cover border border-gray-200 bg-white"
                                    >
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.sector">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.sector') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.sector"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.profession">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_profession') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.profession"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.specialization">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_specialization') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.specialization"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.bio">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.bio') }}</dt>
                                <dd class="mt-2 text-gray-700 whitespace-pre-line leading-relaxed" x-text="user.current_profile.bio"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.experience_years">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.experience') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.experience_years"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.education_level">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.education') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.education_level"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.availability">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.status') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.availability"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.work_modes?.length">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.work_modes') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.work_modes?.join(', ')"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.languages">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.languages') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.languages"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.city">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.city') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.city"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.country">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.country') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.country"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.phone">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_phone') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.phone"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.whatsapp">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_whatsapp') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.whatsapp"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.linkedin_url">
                                <dt class="text-xs text-gray-500">LinkedIn</dt>
                                <dd class="mt-1">
                                    <a :href="user.current_profile.linkedin_url" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 font-medium break-all" x-text="user.current_profile.linkedin_url"></a>
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.portfolio_url">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_portfolio') }}</dt>
                                <dd class="mt-1">
                                    <a :href="user.current_profile.portfolio_url" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 font-medium break-all" x-text="user.current_profile.portfolio_url"></a>
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.github_url">
                                <dt class="text-xs text-gray-500">GitHub</dt>
                                <dd class="mt-1">
                                    <a :href="user.current_profile.github_url" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 font-medium break-all" x-text="user.current_profile.github_url"></a>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section x-show="user.role === 'company'" x-cloak>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('talenma.admin.users.registration_section_current_profile') }}</h4>
                        <template x-if="!user.current_profile || Object.keys(user.current_profile).length === 0">
                            <p class="text-sm text-gray-500 rounded-xl bg-gray-50 px-4 py-3" x-text="labels.currentProfileEmpty"></p>
                        </template>
                        <dl
                            x-show="user.current_profile && Object.keys(user.current_profile).length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"
                        >
                            <div
                                class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2"
                                x-show="user.current_profile?.logo_url || user.current_profile?.representative_photo_url"
                            >
                                <div class="flex flex-wrap gap-6">
                                    <div x-show="user.current_profile?.logo_url">
                                        <dt class="text-xs text-gray-500">{{ __('talenma.company.logo') }}</dt>
                                        <dd class="mt-2">
                                            <img
                                                :src="user.current_profile.logo_url"
                                                :alt="user.current_profile.company_name || user.name"
                                                class="w-20 h-20 rounded-xl object-cover border border-gray-200 bg-white"
                                            >
                                        </dd>
                                    </div>
                                    <div x-show="user.current_profile?.representative_photo_url">
                                        <dt class="text-xs text-gray-500">{{ __('talenma.company.representative_photo') }}</dt>
                                        <dd class="mt-2">
                                            <img
                                                :src="user.current_profile.representative_photo_url"
                                                :alt="user.current_profile.representative_name || user.name"
                                                class="w-20 h-20 rounded-full object-cover border border-gray-200 bg-white"
                                            >
                                        </dd>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.company_name">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_name') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.company_name"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.sector">
                                <dt class="text-xs text-gray-500">{{ __('talenma.auth.sector') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.sector"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.employee_count">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_employees') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.employee_count"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.city">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.city') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.city"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.country">
                                <dt class="text-xs text-gray-500">{{ __('talenma.talent.country') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.country"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.website">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_website') }}</dt>
                                <dd class="mt-1">
                                    <a :href="user.current_profile.website" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 font-medium break-all" x-text="user.current_profile.website"></a>
                                </dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.description">
                                <dt class="text-xs text-gray-500">{{ __('talenma.company.description') }}</dt>
                                <dd class="mt-2 text-gray-700 whitespace-pre-line leading-relaxed" x-text="user.current_profile.description"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3 sm:col-span-2" x-show="user.current_profile?.hiring_needs">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_hiring_needs') }}</dt>
                                <dd class="mt-2 text-gray-700 whitespace-pre-line leading-relaxed" x-text="user.current_profile.hiring_needs"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.representative_name">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_representative') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.representative_name"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.email">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.company_field_email') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900 break-all" x-text="user.current_profile.email"></dd>
                            </div>
                            <div class="rounded-xl border border-gray-200 px-4 py-3" x-show="user.current_profile?.phone">
                                <dt class="text-xs text-gray-500">{{ __('talenma.admin.users.dossier_field_phone') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900" x-text="user.current_profile.phone"></dd>
                            </div>
                        </dl>
                    </section>

                    <section x-show="user.moderator" x-cloak class="rounded-2xl border border-purple-200 bg-purple-50/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-purple-900">{{ __('talenma.admin.users.moderator_section_title') }}</h4>
                                <p class="mt-1 text-xs text-purple-700" x-text="user.moderator?.is_moderator ? labels.moderatorActive : labels.moderatorInactive"></p>
                                <p class="mt-1 text-xs text-purple-600" x-show="user.moderator?.granted_at" x-text="labels.moderatorGrantedAt + ' ' + (user.moderator?.granted_at ?? '')"></p>
                            </div>
                            <template x-if="user.moderator?.can_manage">
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        x-show="!user.moderator.is_moderator"
                                        @click="grantModerator()"
                                        :disabled="moderatorSaving"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-violet-300 text-violet-700 bg-white hover:bg-violet-50 disabled:opacity-50"
                                    >
                                        {{ __('talenma.admin.users.grant_moderator') }}
                                    </button>
                                    <button
                                        type="button"
                                        x-show="user.moderator.is_moderator"
                                        @click="requestModeratorRevoke()"
                                        :disabled="moderatorSaving"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                                    >
                                        {{ __('talenma.admin.users.revoke_moderator') }}
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 space-y-2" x-show="user.moderator?.is_moderator || user.moderator?.can_manage">
                            <p class="text-xs font-semibold uppercase tracking-wide text-purple-800">{{ __('talenma.admin.users.moderator_permissions_title') }}</p>
                            <template x-for="option in (user.moderator?.permission_options ?? [])" :key="option.key">
                                <label class="flex items-start gap-3 rounded-xl border border-purple-100 bg-white px-3 py-2.5">
                                    <input
                                        type="checkbox"
                                        class="mt-0.5 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        :value="option.key"
                                        :checked="selectedPermissions.includes(option.key)"
                                        :disabled="!user.moderator?.can_manage || moderatorSaving || !user.moderator?.is_moderator"
                                        @change="togglePermission(option.key, $event.target.checked)"
                                    >
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-gray-900" x-text="option.label"></span>
                                        <span class="block text-xs text-gray-500" x-text="option.description"></span>
                                    </span>
                                </label>
                            </template>
                            <button
                                type="button"
                                x-show="user.moderator?.can_manage && user.moderator?.is_moderator"
                                @click="saveModeratorPermissions()"
                                :disabled="moderatorSaving"
                                class="mt-2 inline-flex items-center justify-center rounded-lg bg-purple-600 px-3 py-2 text-xs font-semibold text-white hover:bg-purple-700 disabled:opacity-50"
                            >
                                <span x-show="!moderatorSaving">{{ __('talenma.admin.users.moderator_permissions_save') }}</span>
                                <span x-show="moderatorSaving">…</span>
                            </button>
                        </div>
                    </section>
                </div>
            </template>
        </div>

        <div x-show="user?.is_pending && !loading && (user?.can_approve || user?.can_reject)" x-cloak class="shrink-0 border-t border-gray-100 px-6 py-4 bg-gray-50 space-y-3">
            <form x-show="user?.can_approve" :action="user?.approve_url" method="POST">
                @csrf
                <x-primary-button class="w-full justify-center">{{ __('talenma.admin.users.approve_btn') }}</x-primary-button>
            </form>
            <form x-show="user?.can_reject" :action="user?.reject_url" method="POST" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input
                    type="text"
                    name="reason"
                    :placeholder="labels.rejectReason"
                    class="flex-1 text-sm rounded-lg border-gray-300"
                >
                <button type="submit" class="px-4 py-2 text-sm border rounded-lg text-red-700 border-red-200 hover:bg-red-50 whitespace-nowrap">
                    {{ __('talenma.admin.users.reject_btn') }}
                </button>
            </form>
        </div>
    </div>

    <div
        x-show="confirmingModeratorRevoke"
        x-cloak
        class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="moderator-revoke-title"
        @keydown.escape.window="if (confirmingModeratorRevoke) cancelModeratorRevoke()"
    >
        <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]" @click="cancelModeratorRevoke()"></div>
        <div
            x-show="confirmingModeratorRevoke"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 scale-95"
            class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
            @click.stop
        >
            <div class="px-6 pt-6 pb-5">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7 2.6 17a2 2 0 0 0 1.73 3h15.34a2 2 0 0 0 1.73-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 id="moderator-revoke-title" class="text-lg font-bold text-gray-900">
                            {{ __('talenma.admin.users.revoke_moderator_modal_title') }}
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">
                            {{ __('talenma.admin.users.revoke_moderator_modal_text') }}
                            <strong class="font-semibold text-gray-900" x-text="user?.name ?? ''"></strong>
                        </p>
                        <p class="mt-2 rounded-lg bg-purple-50 px-3 py-2 text-sm font-medium text-purple-900">
                            {{ __('talenma.admin.users.revoke_moderator_modal_recovery') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    @click="cancelModeratorRevoke()"
                    class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50"
                >
                    {{ __('talenma.common.cancel') }}
                </button>
                <button
                    type="button"
                    @click="revokeModerator()"
                    :disabled="moderatorSaving"
                    class="inline-flex justify-center rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-800 disabled:opacity-50"
                >
                    {{ __('talenma.admin.users.revoke_moderator_confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>
