@php
    $memberships = $memberships ?? collect();
@endphp

<section id="account-users-card" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company_users.title') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company_users.subtitle') }}</p>
    </div>

    @if ($memberships->isEmpty())
        <p class="text-sm text-gray-500">{{ __('talenma.company_users.empty') }}</p>
    @else
        <ul class="divide-y divide-gray-100 border rounded-xl overflow-hidden">
            @foreach ($memberships as $membership)
                @php $member = $membership->user; @endphp
                <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 bg-white">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $member?->companyDisplayName() ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $member?->email }}</p>
                        @if ($membership->job_title)
                            <p class="mt-0.5 text-xs text-emerald-700">{{ $membership->job_title }}</p>
                        @endif
                    </div>
                    @if ($member)
                        <form
                            method="POST"
                            action="{{ route('company.users.destroy', $member) }}"
                            data-ajax
                            data-confirm="{{ __('talenma.company_users.remove_confirm') }}"
                            data-loading-target="account-users-card"
                            data-refresh="company-users"
                            data-error-message="{{ __('talenma.common.save_error') }}"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center px-3 py-2 text-sm font-semibold text-red-700 border border-red-200 rounded-lg hover:bg-red-50">
                                {{ __('talenma.company_users.remove') }}
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <form
        method="POST"
        action="{{ route('company.users.store') }}"
        class="relative space-y-4 border-t border-gray-100 pt-6"
        data-ajax
        data-ajax-clear="input[type='password']"
        data-loading-target="account-users-card"
        data-refresh="company-users"
        data-error-message="{{ __('talenma.common.save_error') }}"
        novalidate
    >
        @csrf
        <h4 class="text-sm font-semibold text-gray-900">{{ __('talenma.company_users.add') }}</h4>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="member_first_name" :value="__('talenma.company_users.first_name')" />
                <x-text-input
                    id="member_first_name"
                    name="first_name"
                    class="mt-1 block w-full"
                    :value="old('first_name')"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company_users.first_name') }}"
                />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="member_last_name" :value="__('talenma.company_users.last_name')" />
                <x-text-input
                    id="member_last_name"
                    name="last_name"
                    class="mt-1 block w-full"
                    :value="old('last_name')"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company_users.last_name') }}"
                />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="member_email" :value="__('talenma.company_users.email')" />
                <x-text-input
                    id="member_email"
                    name="email"
                    type="email"
                    class="mt-1 block w-full"
                    :value="old('email')"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company_users.email') }}"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="member_job_title" :value="__('talenma.company_users.job_title')" />
                <x-text-input id="member_job_title" name="job_title" class="mt-1 block w-full" :value="old('job_title')" placeholder="{{ __('talenma.company_users.job_title_placeholder') }}" />
                <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="member_password" :value="__('talenma.company_users.password')" />
                <x-text-input
                    id="member_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company_users.password') }}"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="member_password_confirmation" :value="__('talenma.company_users.password_confirmation')" />
                <x-text-input
                    id="member_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    data-required
                    data-required-message="{{ __('talenma.company_users.password_confirmation') }}"
                />
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
            <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
                {{ __('talenma.common.cancel') }}
            </button>
            <x-primary-button class="justify-center">{{ __('talenma.company_users.create') }}</x-primary-button>
        </div>
    </form>
</section>
