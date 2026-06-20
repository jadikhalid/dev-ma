<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.banner.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.admin.banner.subtitle', ['max' => $maxItems]) }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @if (session('banner_saved'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                {{ __('talenma.admin.banner.saved') }}
            </div>
        @endif

        @if (session('banner_deleted'))
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm">
                {{ __('talenma.admin.banner.deleted') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.admin.banner.add_title') }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.banner.add_hint', ['max' => $maxItems]) }}</p>

            <form method="POST" action="{{ route('admin.magazine-banner.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="title" :value="__('talenma.admin.banner.field_title')" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="subtitle" :value="__('talenma.admin.banner.field_subtitle')" />
                    <x-text-input id="subtitle" name="subtitle" class="mt-1 block w-full" :value="old('subtitle')" required />
                    <x-input-error :messages="$errors->get('subtitle')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="url" :value="__('talenma.admin.banner.field_url')" />
                    <x-text-input id="url" name="url" type="url" class="mt-1 block w-full" :value="old('url')" placeholder="https://" required />
                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="thumbnail" :value="__('talenma.admin.banner.field_thumbnail')" />
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.banner.thumbnail_hint') }}</p>
                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                </div>

                <x-primary-button>{{ __('talenma.admin.banner.submit') }}</x-primary-button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">{{ __('talenma.admin.banner.current_list') }}</h3>
                <span class="text-xs font-medium text-gray-500">{{ $items->count() }} / {{ $maxItems }}</span>
            </div>

            @if ($items->isEmpty())
                <p class="p-6 text-sm text-gray-500">{{ __('talenma.admin.banner.empty') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        <li class="p-4 sm:p-5 flex flex-col sm:flex-row gap-4 sm:items-center">
                            <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-400 to-indigo-600 ring-1 ring-gray-200">
                                @if ($item->thumbnailUrl())
                                    <img src="{{ $item->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $item->title }}</p>
                                <p class="text-sm text-gray-600 truncate">{{ $item->subtitle }}</p>
                                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-indigo-600 hover:text-indigo-800 truncate block mt-1">{{ $item->url }}</a>
                                <p class="text-xs text-gray-400 mt-1">{{ $item->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.magazine-banner.destroy', $item) }}" onsubmit="return confirm(@js(__('talenma.admin.banner.delete_confirm')))">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">{{ __('talenma.admin.banner.delete') }}</x-danger-button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
