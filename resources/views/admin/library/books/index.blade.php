<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.library.books_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.admin.library.books_subtitle') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.library.categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    {{ __('talenma.admin.library.nav_categories') }}
                </a>
                <a href="{{ route('admin.library.books.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    {{ __('talenma.admin.library.upload_book') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <form method="GET" class="rounded-2xl border border-gray-100 bg-white p-4 flex flex-col md:flex-row gap-3">
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('talenma.admin.library.search_books') }}" class="flex-1 rounded-xl border-gray-200 text-sm">
            <select name="category" class="md:w-80 rounded-xl border-gray-200 text-sm">
                <option value="">{{ __('talenma.admin.library.all_categories') }}</option>
                @foreach ($leafOptions as $option)
                    <option value="{{ $option['id'] }}" @selected($filters['category'] == $option['id'])>{{ $option['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white">{{ __('talenma.admin.library.filter') }}</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_title') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_category') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_status') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_downloads') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('talenma.admin.library.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($books as $book)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($book->coverUrl())
                                        <img src="{{ $book->coverUrl() }}" alt="" class="h-12 w-9 rounded object-cover border border-gray-100 shrink-0">
                                    @else
                                        <span class="h-12 w-9 rounded bg-amber-50 border border-amber-100 shrink-0"></span>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $book->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $book->author ?: '—' }} · {{ $book->formattedSize() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $book->category_path }}</td>
                            <td class="px-4 py-3">
                                @if ($book->is_published)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ __('talenma.admin.library.published') }}</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-bold text-amber-800">{{ __('talenma.admin.library.draft') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $book->download_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.library.books.edit', $book) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">{{ __('talenma.admin.library.edit') }}</a>
                                    <form method="POST" action="{{ route('admin.library.books.destroy', $book) }}" onsubmit="return confirm(@js(__('talenma.admin.library.delete_book_confirm')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">{{ __('talenma.admin.library.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">{{ __('talenma.admin.library.books_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $books->links() }}
    </div>
</x-app-layout>
