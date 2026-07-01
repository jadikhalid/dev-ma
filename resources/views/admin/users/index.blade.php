<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.users.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.admin.users.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @foreach (['request_submitted' => 'amber', 'user_created' => 'green', 'user_approved' => 'green', 'user_rejected' => 'amber', 'user_deleted' => 'amber', 'moderator_granted' => 'green', 'moderator_revoked' => 'amber', 'request_approved' => 'green', 'request_rejected' => 'amber'] as $flash => $color)
            @if (session($flash))
                <div class="p-4 rounded-xl border text-sm {{ $color === 'green' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
                    {{ __('talenma.admin.users.flash.'.$flash) }}
                </div>
            @endif
        @endforeach

        @if (Auth::user()->isAdmin() && $pendingRequests->isNotEmpty())
            <section class="bg-violet-50 border border-violet-200 rounded-2xl p-6 space-y-4">
                <h3 class="font-semibold text-violet-900">{{ __('talenma.admin.users.pending_requests_title') }}</h3>
                @foreach ($pendingRequests as $moderationRequest)
                    <div class="bg-white rounded-xl border p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ __('talenma.admin.users.action_labels.'.$moderationRequest->action_type) }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('talenma.admin.users.requested_by', ['name' => $moderationRequest->requester->name]) }}
                                @if ($moderationRequest->targetUser)
                                    — {{ $moderationRequest->targetUser->name }} ({{ $moderationRequest->targetUser->email }})
                                @endif
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.moderation.approve', $moderationRequest) }}">
                                @csrf
                                <x-primary-button>{{ __('talenma.admin.users.confirm_action') }}</x-primary-button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.reject', $moderationRequest) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm border rounded-lg text-gray-700 hover:bg-gray-50">
                                    {{ __('talenma.admin.users.refuse_action') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        <section class="bg-white rounded-2xl border p-6 sm:p-8">
            <h3 class="font-semibold text-gray-900">{{ __('talenma.admin.users.add_title') }}</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('talenma.auth.full_name')" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('talenma.auth.email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password" :value="__('talenma.auth.password')" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="role" :value="__('talenma.admin.users.role')" />
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="dev" @selected(old('role') === 'dev')>{{ __('talenma.auth.role_talent') }}</option>
                        <option value="company" @selected(old('role') === 'company')>{{ __('talenma.auth.role_company') }}</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="approve_immediately" value="1" class="rounded text-indigo-600" @checked(old('approve_immediately'))>
                        {{ __('talenma.admin.users.approve_immediately') }}
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <x-primary-button>{{ __('talenma.admin.users.add_btn') }}</x-primary-button>
                </div>
            </form>
        </section>

        <section class="bg-white rounded-2xl border overflow-hidden">
            <div class="px-6 py-4 border-b flex flex-wrap gap-2">
                @foreach (['pending' => __('talenma.admin.users.filter_pending'), 'talents' => __('talenma.admin.users.filter_talents'), 'companies' => __('talenma.admin.users.filter_companies'), 'moderators' => __('talenma.admin.users.filter_moderators'), 'all' => __('talenma.admin.users.filter_all')] as $key => $label)
                    <a href="{{ route('admin.users.index', ['filter' => $key]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $filter === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                        @if ($key === 'pending' && $pendingCount > 0)
                            <span class="ml-1">({{ $pendingCount }})</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="divide-y">
                @forelse ($users as $user)
                    <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <p class="text-xs mt-1 text-gray-400">
                                {{ __('talenma.admin.users.role_label') }} :
                                @if ($user->isModerator())
                                    {{ __('talenma.roles.moderator') }}
                                @elseif ($user->isCompany())
                                    {{ __('talenma.roles.company') }}
                                @else
                                    {{ __('talenma.roles.talent') }}
                                @endif
                                @if ($user->isTalent())
                                    — {{ __('talenma.admin.users.status_'.$user->approval_status) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if ($user->isTalent() && $user->isPendingApproval())
                                <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                    @csrf
                                    <x-primary-button>{{ __('talenma.admin.users.approve_btn') }}</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="reason" placeholder="{{ __('talenma.admin.users.reject_reason') }}" class="text-sm rounded-lg border-gray-300">
                                    <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-red-700 border-red-200 hover:bg-red-50">
                                        {{ __('talenma.admin.users.reject_btn') }}
                                    </button>
                                </form>
                            @endif

                            @if (Auth::user()->isAdmin() && $user->isTalent() && $user->isApproved())
                                <form method="POST" action="{{ route('admin.users.moderator.grant', $user) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-violet-700 border-violet-200 hover:bg-violet-50">
                                        {{ __('talenma.admin.users.grant_moderator') }}
                                    </button>
                                </form>
                            @endif

                            @if (Auth::user()->isAdmin() && $user->isModerator())
                                <form method="POST" action="{{ route('admin.users.moderator.revoke', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-gray-700 hover:bg-gray-50">
                                        {{ __('talenma.admin.users.revoke_moderator') }}
                                    </button>
                                </form>
                            @endif

                            @if (! $user->isAdmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm(@js(__('talenma.admin.users.delete_confirm')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm border rounded-lg text-red-700 border-red-200 hover:bg-red-50">
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
        </section>
    </div>
</x-app-layout>
