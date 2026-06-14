<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.recruitment.title') }}</h2>
            <p class="text-sm text-gray-500">{{ $talent ? __('talenma.recruitment.subtitle_talent', ['name' => $talent->name]) : __('talenma.recruitment.subtitle_general') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <form method="POST" action="{{ route('recruitment.store') }}" class="space-y-6">@csrf
                @if ($talent)
                    <input type="hidden" name="developer_user_id" value="{{ $talent->id }}">
                    <div class="p-4 bg-indigo-50 rounded-xl text-sm">
                        <strong>{{ __('talenma.talents.target') }}</strong> {{ $talent->name }} — {{ $talent->profile->title ?? '' }}
                    </div>
                @endif

                <div>
                    <x-input-label :value="__('talenma.recruitment.mode')" />
                    <div class="mt-2 grid sm:grid-cols-2 gap-3">
                        <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="mode" value="intermediary" class="mt-1" {{ $mode === 'intermediary' ? 'checked' : '' }}>
                            <div><span class="font-semibold text-sm">{{ __('talenma.recruitment.mode_inter') }}</span><p class="text-xs text-gray-500 mt-0.5">{{ __('talenma.recruitment.mode_inter_desc') }}</p></div>
                        </label>
                        <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="mode" value="direct" class="mt-1" {{ $mode === 'direct' ? 'checked' : '' }}>
                            <div><span class="font-semibold text-sm">{{ __('talenma.recruitment.mode_direct') }}</span><p class="text-xs text-gray-500 mt-0.5">{{ __('talenma.recruitment.mode_direct_desc') }}</p></div>
                        </label>
                    </div>
                </div>

                <div>
                    <x-input-label for="subject" :value="__('talenma.recruitment.subject')" />
                    <x-text-input id="subject" name="subject" class="mt-1 block w-full" :value="old('subject', $talent ? 'Recrutement : ' . $talent->name : '')" required />
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" :value="__('talenma.recruitment.message')" />
                    <textarea id="message" name="message" rows="5" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" required placeholder="Stack recherchée, durée de mission, budget, délai…">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button>{{ __('talenma.recruitment.send') }}</x-primary-button>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900">{{ __('talenma.recruitment.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
