<div id="publications-news-list" class="relative bg-white rounded-2xl border overflow-hidden" x-data>
    <div class="px-4 sm:px-5 py-3 border-b bg-gray-50 flex items-center justify-between gap-3">
        <h4 class="text-sm font-semibold text-gray-900">{{ __('talenma.admin.news.current_list') }}</h4>
        <span class="text-xs font-medium text-gray-500 tabular-nums">{{ $newsItems->count() }} / {{ $newsMaxItems }}</span>
    </div>

    @if ($newsItems->isEmpty())
        <p class="p-5 text-sm text-gray-500">{{ __('talenma.admin.news.empty') }}</p>
    @else
        <ul class="divide-y divide-gray-100">
            @foreach ($newsItems as $item)
                <li class="p-4 flex gap-3">
                    <div class="shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-400 to-indigo-600 ring-1 ring-gray-200">
                        @if ($item->thumbnailUrl())
                            <img src="{{ $item->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] text-gray-400">{{ $item->created_at->translatedFormat('d M Y H:i') }}</p>
                        <p class="mt-0.5 text-sm font-semibold text-gray-900 truncate">{{ $item->title }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ $item->subtitle }}</p>
                        <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-indigo-600 hover:text-indigo-800 truncate block mt-1">{{ $item->url }}</a>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-secondary-button
                                type="button"
                                @click="$dispatch('publications-news-edit', {{ Js::from([
                                    'id' => $item->id,
                                    'title' => $item->title,
                                    'subtitle' => $item->subtitle,
                                    'url' => $item->url,
                                    'action' => route('admin.publications.news.update', $item),
                                ]) }})"
                            >
                                {{ __('talenma.admin.news.edit') }}
                            </x-secondary-button>
                            <form
                                id="publications-news-delete-{{ $item->id }}"
                                method="POST"
                                action="{{ route('admin.publications.news.destroy', $item) }}"
                                class="hidden"
                                data-ajax
                                data-refresh="publications-news"
                                data-loading-target="publications-news-list"
                                data-error-message="{{ __('talenma.common.save_error') }}"
                            >
                                @csrf
                                @method('DELETE')
                            </form>
                            <x-danger-button
                                type="button"
                                @click="$dispatch('publications-delete', {{ Js::from([
                                    'formId' => 'publications-news-delete-'.$item->id,
                                    'title' => $item->title,
                                    'modalTitle' => __('talenma.admin.news.delete_modal_title'),
                                    'modalBody' => __('talenma.admin.news.delete_modal_body'),
                                    'confirmLabel' => __('talenma.admin.news.delete_confirm_btn'),
                                ]) }})"
                            >
                                {{ __('talenma.admin.news.delete') }}
                            </x-danger-button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
