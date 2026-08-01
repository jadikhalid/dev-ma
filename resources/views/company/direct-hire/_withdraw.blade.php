@php
    $hireRoute = $hireRoute ?? 'company.direct-hire';
@endphp
<div id="direct-hire-withdraw" class="bg-white rounded-2xl border p-6">
    <form method="POST" action="{{ route($hireRoute.'.withdraw', $directHire) }}" class="space-y-3" onsubmit="return confirm(@js(__('talenma.direct_hire.withdraw_confirm')))">
        @csrf
        <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.withdraw_title') }}</p>
        <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
        <button type="submit" class="inline-flex px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">{{ __('talenma.direct_hire.withdraw_btn') }}</button>
    </form>
</div>
