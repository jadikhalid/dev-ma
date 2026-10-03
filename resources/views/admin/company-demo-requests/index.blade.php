@php
    use App\Models\CompanyDemoRequest;

    $statusLabels = [
        CompanyDemoRequest::STATUS_NEW => __('talenma.admin.company_demo_requests.filter_new'),
        CompanyDemoRequest::STATUS_SCHEDULED => __('talenma.admin.company_demo_requests.filter_scheduled'),
        CompanyDemoRequest::STATUS_DONE => __('talenma.admin.company_demo_requests.filter_done'),
    ];
    $actionLabels = [
        CompanyDemoRequest::STATUS_NEW => __('talenma.admin.company_demo_requests.action_new'),
        CompanyDemoRequest::STATUS_SCHEDULED => __('talenma.admin.company_demo_requests.action_scheduled'),
        CompanyDemoRequest::STATUS_DONE => __('talenma.admin.company_demo_requests.action_done'),
    ];
@endphp

<x-app-layout>
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('talenma.admin.company_demo_requests.title') }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.company_demo_requests.subtitle') }}</p>
            </div>
            @if ($newCount > 0)
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800" data-demo-new-count>
                    {{ __('talenma.admin.company_demo_requests.new_badge', ['count' => $newCount]) }}
                </span>
            @endif
        </div>

        <div class="mt-6 flex gap-4 border-b border-gray-200 text-sm font-medium">
            @foreach ($statusLabels as $key => $label)
                <a
                    href="{{ route('admin.company-demo-requests.index', ['status' => $key]) }}"
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
                            <th class="px-4 py-3">{{ __('talenma.admin.company_demo_requests.col_company') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_demo_requests.col_contact') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_demo_requests.col_email') }}</th>
                            <th class="px-4 py-3">{{ __('talenma.admin.company_demo_requests.col_date') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('talenma.admin.company_demo_requests.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($requests as $demo)
                            <tr class="align-top" data-demo-request="{{ $demo->id }}">
                                <td class="px-4 py-4 font-semibold text-gray-900">{{ $demo->company_name }}</td>
                                <td class="px-4 py-4">
                                    <div class="text-gray-800">{{ $demo->contact_name }}</div>
                                    @if (filled($demo->phone))
                                        <div class="mt-0.5 text-xs text-gray-500">{{ $demo->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <a href="mailto:{{ $demo->email }}" class="text-indigo-600 hover:underline">{{ $demo->email }}</a>
                                </td>
                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">{{ $demo->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col items-end gap-2">
                                        @foreach ($actionLabels as $target => $actionLabel)
                                            @continue($target === $demo->status)
                                            <form method="POST" action="{{ route('admin.company-demo-requests.status', $demo) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $target }}">
                                                <button
                                                    type="submit"
                                                    @class([
                                                        'inline-flex rounded-lg px-3 py-1.5 text-xs font-semibold transition',
                                                        'bg-indigo-600 text-white hover:bg-indigo-700' => $target === CompanyDemoRequest::STATUS_SCHEDULED,
                                                        'bg-emerald-600 text-white hover:bg-emerald-700' => $target === CompanyDemoRequest::STATUS_DONE,
                                                        'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50' => $target === CompanyDemoRequest::STATUS_NEW,
                                                    ])
                                                >{{ $actionLabel }}</button>
                                            </form>
                                        @endforeach
                                        @if ($demo->handler && $demo->handled_at)
                                            <span class="text-right text-[11px] text-gray-400">
                                                {{ __('talenma.admin.company_demo_requests.handled_by', [
                                                    'name' => $demo->handler->name,
                                                    'date' => $demo->handled_at->format('d/m/Y H:i'),
                                                ]) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php($needs = $demo->needsSummary())
                            @if ($needs !== [] || filled($demo->message))
                                <tr class="bg-gray-50/70">
                                    <td colspan="5" class="px-4 py-3 text-xs leading-relaxed text-gray-600">
                                        @if ($needs !== [])
                                            <dl class="flex flex-wrap gap-x-5 gap-y-1" data-demo-needs>
                                                @foreach ($needs as $label => $value)
                                                    <div class="flex gap-1">
                                                        <dt class="font-semibold text-gray-700">{{ $label }} :</dt>
                                                        <dd>{{ $value }}</dd>
                                                    </div>
                                                @endforeach
                                            </dl>
                                        @endif
                                        @if (filled($demo->message))
                                            <p @class(['whitespace-pre-wrap', 'mt-2' => $needs !== []])>{{ $demo->message }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">
                                    {{ __('talenma.admin.company_demo_requests.empty') }}
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
</x-app-layout>
