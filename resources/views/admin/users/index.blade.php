<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4" x-data>
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.users.title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.admin.users.subtitle') }}</p>
            </div>
            @if ($canCreateAccounts ?? Auth::user()->isAdmin())
            <button
                type="button"
                @click="$dispatch('open-create-account')"
                class="shrink-0 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-3 py-2 sm:px-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="{{ __('talenma.admin.users.add_title') }}"
                aria-label="{{ __('talenma.admin.users.add_title') }}"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span class="hidden sm:inline whitespace-nowrap">{{ __('talenma.admin.users.add_open') }}</span>
            </button>
            @endif
        </div>
    </x-slot>

    <x-process-help topic="users" />

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @foreach (['user_created' => 'green', 'user_approved' => 'green', 'user_rejected' => 'amber', 'user_deleted' => 'amber', 'moderator_granted' => 'green', 'moderator_revoked' => 'amber'] as $flash => $color)
            @if (session($flash))
                <div class="p-4 rounded-xl border text-sm {{ $color === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
                    {{ __('talenma.admin.users.flash.'.$flash) }}
                </div>
            @endif
        @endforeach

        <div id="admin-users-dynamic" class="space-y-8">
            <section
                id="admin-users-list"
                class="relative bg-white rounded-2xl border overflow-hidden"
                @if (in_array($filter, ['pending', 'talents', 'companies', 'moderators', 'all'], true))
                    x-data="adminPendingDrawer({
                        loadError: @js(__('talenma.admin.users.registration_load_error')),
                        saveError: @js(__('talenma.common.save_error')),
                        labels: {
                            drawerTitle: @js(__('talenma.admin.users.registration_drawer_title')),
                            close: @js(__('talenma.admin.users.registration_close')),
                            emailVerified: @js(__('talenma.admin.users.registration_email_verified')),
                            emailUnverified: @js(__('talenma.admin.users.registration_email_unverified')),
                            rejectReason: @js(__('talenma.admin.users.reject_reason')),
                            currentProfileEmpty: @js(__('talenma.admin.users.registration_current_profile_empty')),
                            moderatorActive: @js(__('talenma.admin.users.moderator_active')),
                            moderatorInactive: @js(__('talenma.admin.users.moderator_inactive')),
                            moderatorGrantedAt: @js(__('talenma.admin.users.moderator_granted_at')),
                        },
                    })"
                @endif
            >
            <div class="px-6 py-4 border-b space-y-3">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-2">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <div class="relative flex-1">
                        <label for="admin-users-search" class="sr-only">{{ __('talenma.admin.users.search_label') }}</label>
                        <input
                            id="admin-users-search"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="{{ __('talenma.admin.users.search_placeholder') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            autocomplete="off"
                        >
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button type="submit">{{ __('talenma.admin.users.search_btn') }}</x-primary-button>
                        @if ($search !== '')
                            <a href="{{ route('admin.users.index', ['filter' => $filter]) }}" class="inline-flex items-center px-3 py-2 text-sm rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">
                                {{ __('talenma.admin.users.search_clear') }}
                            </a>
                        @endif
                    </div>
                </form>

                <div class="flex flex-wrap gap-2">
                    @foreach (['pending' => __('talenma.admin.users.filter_pending'), 'talents' => __('talenma.admin.users.filter_talents'), 'companies' => __('talenma.admin.users.filter_companies'), 'moderators' => __('talenma.admin.users.filter_moderators'), 'all' => __('talenma.admin.users.filter_all')] as $key => $label)
                        <a href="{{ route('admin.users.index', array_filter(['filter' => $key, 'q' => $search !== '' ? $search : null])) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $filter === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $label }}
                            @if ($key === 'pending' && $pendingCount > 0)
                                <span class="ml-1">({{ $pendingCount }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="divide-y">
                @forelse ($users as $user)
                    @php
                        $isClickable = in_array($filter, ['pending', 'talents', 'companies', 'moderators', 'all'], true)
                            && ($user->isTalent() || $user->isCompany())
                            && $user->hasVerifiedEmail();
                    @endphp
                    <div
                        id="admin-user-row-{{ $user->id }}"
                        @class([
                            'px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4',
                            'cursor-pointer hover:bg-indigo-50/50 transition-colors' => $isClickable,
                        ])
                        @if ($isClickable)
                            role="button"
                            tabindex="0"
                            aria-label="{{ __('talenma.admin.users.view_registration') }} — {{ $user->name }}"
                            @click="openFor({{ $user->id }})"
                            @keydown.enter.prevent="openFor({{ $user->id }})"
                        @endif
                    >
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    <p class="text-xs mt-1 text-gray-400">
                                        {{ __('talenma.admin.users.role_label') }} :
                                        @if ($user->isModerator())
                                            <span class="inline-flex items-center rounded-md bg-purple-50 px-1.5 py-0.5 text-[11px] font-semibold text-purple-800 ring-1 ring-purple-200">{{ __('talenma.roles.moderator') }}</span>
                                        @elseif ($user->isCompany())
                                            {{ __('talenma.roles.company') }}
                                        @else
                                            {{ __('talenma.roles.talent') }}
                                        @endif
                                        @if ($user->isTalent() || $user->isCompany())
                                            — {{ __('talenma.admin.users.status_'.$user->approval_status) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2" @if ($isClickable) @click.stop @keydown.stop @endif>
                            @if (($user->isTalent() || $user->isCompany()) && $user->isPendingApproval())
                                @if ($canApproveAccounts ?? false)
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                        @csrf
                                        <x-primary-button>{{ __('talenma.admin.users.approve_btn') }}</x-primary-button>
                                    </form>
                                @endif
                                @if ($canRejectAccounts ?? false)
                                    <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="reason" placeholder="{{ __('talenma.admin.users.reject_reason') }}" class="text-sm rounded-lg border-gray-300">
                                        <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-red-700 border-red-200 hover:bg-red-50">
                                            {{ __('talenma.admin.users.reject_btn') }}
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if (
                                ($canEditProfiles ?? false)
                                && ($user->isTalent() || $user->isCompany())
                                && ! $user->isAdmin()
                            )
                                <a
                                    href="{{ route('admin.users.profile.edit', $user) }}"
                                    class="px-3 py-2 text-sm border rounded-lg text-indigo-700 border-indigo-200 hover:bg-indigo-50"
                                >
                                    {{ __('talenma.admin.users.edit_profile_btn') }}
                                </a>
                            @endif

                            @if (
                                ($canDeleteAccounts ?? false)
                                && ! $user->isAdmin()
                                && (! $user->isModerator() || Auth::user()->isAdmin())
                            )
                                <form
                                    id="admin-user-delete-form-{{ $user->id }}"
                                    method="POST"
                                    action="{{ route('admin.users.destroy', $user) }}"
                                    data-ajax
                                    data-refresh="admin-users"
                                    data-loading-target="admin-user-row-{{ $user->id }}"
                                    data-error-message="{{ __('talenma.common.save_error') }}"
                                    data-network-error-message="{{ __('talenma.common.network_error') }}"
                                    data-timeout-error-message="{{ __('talenma.common.timeout_error') }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        @click="$dispatch('open-delete-user', {
                                            formId: 'admin-user-delete-form-{{ $user->id }}',
                                            userName: @js($user->name),
                                            deletesMembers: @js($user->isCompanyOwner()),
                                            isModerator: @js($user->isModerator()),
                                        })"
                                        class="px-3 py-2 text-sm border rounded-lg text-red-700 border-red-200 hover:bg-red-50"
                                    >
                                        {{ __('talenma.admin.users.delete_btn') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-sm text-gray-500">{{ __('talenma.admin.users.empty') }}</p>
                @endforelse
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t">{{ $users->links() }}</div>
            @endif

            @if (in_array($filter, ['pending', 'talents', 'companies', 'moderators', 'all'], true))
                <x-admin.pending-registration-drawer />
            @endif
            </section>
        </div>

        <div
            x-data="adminUserDeletionModal"
            x-on:open-delete-user.window="requestDeletion($event.detail)"
            @keydown.escape.window="if (open) cancelDeletion()"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="admin-delete-user-title"
        >
            <div
                x-show="open"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]"
                @click="cancelDeletion()"
            ></div>

            <div
                x-show="open"
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
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7 2.6 17a2 2 0 0 0 1.73 3h15.34a2 2 0 0 0 1.73-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 id="admin-delete-user-title" class="text-lg font-bold text-gray-900">
                                {{ __('talenma.admin.users.delete_modal_title') }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                {{ __('talenma.admin.users.delete_modal_text') }}
                                <strong class="font-semibold text-gray-900" x-text="userName"></strong>
                            </p>
                            <p class="mt-2 text-sm font-medium text-red-700">
                                {{ __('talenma.admin.users.delete_modal_warning') }}
                            </p>
                            <p
                                x-show="deletesMembers"
                                x-cloak
                                class="mt-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-800"
                            >
                                {{ __('talenma.admin.users.delete_modal_company_members_warning') }}
                            </p>
                            <p
                                x-show="isModerator"
                                x-cloak
                                class="mt-2 rounded-lg bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-900"
                            >
                                {{ __('talenma.admin.users.delete_modal_moderator_warning') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        x-ref="cancelButton"
                        @click="cancelDeletion()"
                        class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ __('talenma.common.cancel') }}
                    </button>
                    <button
                        type="button"
                        @click="confirmDeletion()"
                        class="inline-flex justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        {{ __('talenma.admin.users.delete_confirm_btn') }}
                    </button>
                </div>
            </div>
        </div>

        @if ($canCreateAccounts ?? Auth::user()->isAdmin())
        <div
            x-data="adminCreateAccountDrawer({
                checkEmailUrl: @js(route('admin.users.check-email')),
                messages: {
                    emailAvailable: @js(__('talenma.admin.users.email_available')),
                    emailChecking: @js(__('talenma.admin.users.email_checking')),
                    emailTaken: @js(__('talenma.auth.validation.email_taken')),
                    emailInvalid: @js(__('talenma.auth.validation.email_invalid')),
                    networkError: @js(__('talenma.common.network_error')),
                },
            })"
            x-on:open-create-account.window="openDrawer()"
            x-on:ajax-form-success.window="onAjaxSuccess($event)"
            @keydown.escape.window="if (open) closeDrawer()"
        >
            <div
                x-show="open"
                x-cloak
                class="fixed inset-0 z-50"
                role="dialog"
                aria-modal="true"
                aria-label="{{ __('talenma.admin.users.add_title') }}"
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
                    @click="closeDrawer()"
                ></div>

                <div
                    id="admin-create-account-drawer"
                    x-show="open"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full"
                    x-transition:enter-end="translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0"
                    x-transition:leave-end="translate-y-full"
                    class="absolute inset-x-0 bottom-0 w-full rounded-t-2xl sm:inset-x-auto sm:right-6 sm:bottom-6 sm:max-w-xl md:max-w-2xl sm:rounded-2xl max-h-[85vh] flex flex-col bg-white shadow-2xl"
                    @click.stop
                >
                    <div class="shrink-0 flex items-center justify-between gap-4 px-6 sm:px-7 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">{{ __('talenma.admin.users.add_title') }}</h3>
                        <button
                            type="button"
                            @click="closeDrawer()"
                            class="shrink-0 p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                            aria-label="{{ __('talenma.admin.users.registration_close') }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form
                        id="admin-create-account-form"
                        method="POST"
                        action="{{ route('admin.users.store') }}"
                        class="flex-1 min-h-0 flex flex-col"
                        data-ajax
                        data-ajax-reset
                        data-refresh="admin-users"
                        data-loading-target="admin-create-account-drawer"
                        data-error-message="{{ __('talenma.common.save_error') }}"
                        data-network-error-message="{{ __('talenma.common.network_error') }}"
                        data-timeout-error-message="{{ __('talenma.common.timeout_error') }}"
                        novalidate
                        x-ref="form"
                    >
                        @csrf
                        <div class="flex-1 min-h-0 overflow-y-auto px-6 sm:px-7 py-6 grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-input-label for="create_role" :value="__('talenma.admin.users.role')" />
                                <select
                                    id="create_role"
                                    name="role"
                                    x-model="role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                >
                                    <option value="dev">{{ __('talenma.auth.role_talent') }}</option>
                                    <option value="company">{{ __('talenma.auth.role_company') }}</option>
                                </select>
                            </div>

                            <div x-show="isCompany" x-cloak class="sm:col-span-2">
                                <x-input-label for="create_company_name" :value="__('talenma.auth.company_name')" />
                                <x-text-input
                                    id="create_company_name"
                                    name="company_name"
                                    class="mt-1 block w-full"
                                    autocomplete="organization"
                                    minlength="2"
                                    maxlength="255"
                                    x-bind:required="isCompany"
                                    x-bind:disabled="!isCompany"
                                    x-bind:data-required="isCompany ? '' : null"
                                    data-required-message="{{ __('talenma.auth.validation.name_required') }}"
                                    data-min-length="2"
                                    data-min-length-message="{{ __('talenma.auth.validation.name_min') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="create_first_name">
                                    <span x-text="isCompany ? @js(__('talenma.auth.representative_first_name')) : @js(__('talenma.auth.first_name'))"></span>
                                </x-input-label>
                                <x-text-input
                                    id="create_first_name"
                                    name="first_name"
                                    x-ref="createAccountFirstName"
                                    class="mt-1 block w-full"
                                    autocomplete="given-name"
                                    minlength="2"
                                    maxlength="127"
                                    required
                                    data-required
                                    x-bind:data-required-message="isCompany ? @js(__('talenma.auth.validation.representative_first_name_required')) : @js(__('talenma.auth.validation.first_name_required'))"
                                    data-min-length="2"
                                    data-min-length-message="{{ __('talenma.auth.validation.first_name_min') }}"
                                />
                            </div>
                            <div>
                                <x-input-label for="create_last_name">
                                    <span x-text="isCompany ? @js(__('talenma.auth.representative_last_name')) : @js(__('talenma.auth.last_name'))"></span>
                                </x-input-label>
                                <x-text-input
                                    id="create_last_name"
                                    name="last_name"
                                    class="mt-1 block w-full"
                                    autocomplete="family-name"
                                    minlength="2"
                                    maxlength="127"
                                    required
                                    data-required
                                    x-bind:data-required-message="isCompany ? @js(__('talenma.auth.validation.representative_last_name_required')) : @js(__('talenma.auth.validation.last_name_required'))"
                                    data-min-length="2"
                                    data-min-length-message="{{ __('talenma.auth.validation.last_name_min') }}"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="create_email" :value="__('talenma.auth.email')" />
                                <x-text-input
                                    id="create_email"
                                    name="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    autocomplete="email"
                                    maxlength="255"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.auth.validation.email_required') }}"
                                    data-email
                                    data-email-message="{{ __('talenma.auth.validation.email_invalid') }}"
                                    @blur="checkEmail()"
                                    @input="clearEmailStatus()"
                                />
                                <p
                                    x-show="emailStatus"
                                    x-cloak
                                    class="mt-2 text-xs"
                                    :class="{
                                        'text-gray-500': emailStatus === 'checking',
                                        'text-emerald-700': emailStatus === 'available',
                                        'text-red-600': emailStatus === 'taken' || emailStatus === 'invalid' || emailStatus === 'error',
                                    }"
                                    x-text="emailMessage"
                                ></p>
                            </div>
                            <div>
                                <x-input-label for="create_password" :value="__('talenma.auth.password')" />
                                <x-text-input
                                    id="create_password"
                                    name="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    autocomplete="new-password"
                                    minlength="8"
                                    maxlength="128"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.auth.validation.password_required') }}"
                                    data-min-length="8"
                                    data-min-length-message="{{ __('talenma.auth.validation.password_min') }}"
                                />
                            </div>
                            <div>
                                <x-input-label for="create_password_confirmation" :value="__('talenma.auth.confirm_password')" />
                                <x-text-input
                                    id="create_password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    autocomplete="new-password"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.auth.validation.password_confirmed') }}"
                                    data-match="#create_password"
                                    data-match-message="{{ __('talenma.auth.validation.password_confirmed') }}"
                                />
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-wrap items-center gap-3 px-6 sm:px-7 py-4 border-t border-gray-100 bg-gray-50 sm:rounded-b-2xl">
                            <x-primary-button type="submit">{{ __('talenma.admin.users.add_btn') }}</x-primary-button>
                            <button type="button" @click="resetForm()" class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-700 hover:bg-white">
                                {{ __('talenma.common.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
