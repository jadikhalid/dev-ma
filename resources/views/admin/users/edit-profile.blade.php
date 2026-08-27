<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.users.edit_profile_title') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $profileKind === 'company'
                        ? __('talenma.admin.users.edit_profile_subtitle_company', ['name' => $targetUser->name])
                        : __('talenma.admin.users.edit_profile_subtitle_talent', ['name' => $targetUser->formalDisplayName()]) }}
                </p>
            </div>
            <a
                href="{{ route('admin.users.index', ['filter' => $profileKind === 'company' ? 'companies' : 'talents']) }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 shrink-0"
            >
                ← {{ __('talenma.admin.users.edit_profile_back') }}
            </a>
        </div>
    </x-slot>

    <div
        class="py-10"
        data-ajax-network-error="{{ __('talenma.common.network_error') }}"
        data-ajax-timeout-error="{{ __('talenma.common.timeout_error') }}"
    >
        <x-toast-stack />
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3 text-sm text-indigo-900">
                <span class="font-semibold">{{ $targetUser->email }}</span>
                <span class="text-indigo-700/80">—</span>
                {{ $profileKind === 'company' ? __('talenma.roles.company') : __('talenma.roles.talent') }}
            </div>

            @if ($profileKind === 'company')
                @include('company.partials.fiche-content')
            @else
                @include('admin.users._talent-avatar-form', [
                    'user' => $targetUser,
                    'avatarUpdateUrl' => $avatarUpdateUrl,
                ])
                @include('talent.partials.fiche-content')
            @endif
        </div>
    </div>
</x-app-layout>
