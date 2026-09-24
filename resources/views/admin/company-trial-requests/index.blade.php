@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('talenma.admin.company_trial_requests.title') }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.company_trial_requests.subtitle') }}</p>
            </div>
            @if ($pendingCount > 0)
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                    {{ __('talenma.admin.company_trial_requests.pending_badge', ['count' => $pendingCount]) }}
                </span>
            @endif
        </div>

        <div class="mt-6 flex gap-4 border-b border-gray-200 text-sm font-medium">
            @foreach ([
                'pending' => __('talenma.admin.company_trial_requests.filter_pending'),
                'provisioned' => __('talenma.admin.company_trial_requests.filter_provisioned'),
                'rejected' => __('talenma.admin.company_trial_requests.filter_rejected'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.company-trial-requests.index', ['status' => $key]) }}"
                    @class([
                        '-mb-px border-b-2 pb-2.5 transition',
                        'border-indigo-600 text-indigo-700' => $status === $key,
                        'border-transparent text-gray-500 hover:text-gray-800' => $status !== $key,
                    ])
                >{{ $label }}</a>
            @endforeach
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_trial_requests.col_company') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_trial_requests.col_contact') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_trial_requests.col_email') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_trial_requests.col_date') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('talenma.admin.company_trial_requests.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($requests as $trial)
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-gray-900">{{ $trial->company_name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500">{{ $trial->sector }} · {{ strtoupper($trial->company_country) }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-gray-800">{{ $trial->contact_name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500">{{ $trial->phone }}</div>
                                </td>
                                <td class="px-4 py-4 text-gray-700">{{ $trial->email }}</td>
                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">{{ $trial->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col items-end gap-2">
                                        @if ($trial->isPending())
                                            <form method="POST" action="{{ route('admin.company-trial-requests.provision', $trial) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                    {{ __('talenma.admin.company_trial_requests.action_provision') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.company-trial-requests.reject', $trial) }}" class="w-full max-w-xs">
                                                @csrf
                                                <input
                                                    type="text"
                                                    name="rejection_reason"
                                                    maxlength="2000"
                                                    placeholder="{{ __('talenma.admin.company_trial_requests.reject_reason_placeholder') }}"
                                                    class="mb-1.5 w-full rounded-md border-gray-300 text-xs"
                                                >
                                                <button type="submit" class="inline-flex rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                    {{ __('talenma.admin.company_trial_requests.action_reject') }}
                                                </button>
                                            </form>
                                        @elseif ($trial->status === \App\Models\CompanyTrialRequest::STATUS_PROVISIONED)
                                            <span class="text-xs font-semibold text-emerald-700">{{ __('talenma.admin.company_trial_requests.status_provisioned') }}</span>
                                            @if ($trial->user_id)
                                                <a href="{{ route('admin.users.index', ['filter' => 'companies', 'q' => $trial->email]) }}" class="text-xs font-medium text-indigo-600 hover:underline">
                                                    {{ __('talenma.admin.company_trial_requests.view_account') }}
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs font-semibold text-rose-700">{{ __('talenma.admin.company_trial_requests.status_rejected') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr class="bg-gray-50/70">
                                <td colspan="5" class="px-4 py-3 text-xs leading-relaxed text-gray-600 whitespace-pre-wrap">{{ $trial->company_description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                    {{ __('talenma.admin.company_trial_requests.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
