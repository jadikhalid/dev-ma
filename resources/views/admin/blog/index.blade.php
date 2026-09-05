<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.blog.admin.title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.blog.admin.subtitle') }}</p>
            </div>
            <a
                href="{{ route('admin.blog.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition"
            >
                {{ __('talenma.blog.admin.create') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('talenma.blog.admin.col_title') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.blog.admin.col_locale') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.blog.admin.col_status') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.blog.admin.col_ticker') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('talenma.blog.admin.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900">{{ $post->title }}</p>
                                <p class="text-xs text-gray-400">/blog/{{ $post->slug }}</p>
                            </td>
                            <td class="px-4 py-3 uppercase text-gray-600">{{ $post->locale }}</td>
                            <td class="px-4 py-3">
                                @if ($post->status === \App\Models\BlogPost::STATUS_PUBLISHED)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ __('talenma.blog.admin.status_published') }}</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-xs font-bold text-amber-800">{{ __('talenma.blog.admin.status_draft') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $post->show_in_ticker ? __('talenma.blog.admin.ticker_yes') : __('talenma.blog.admin.ticker_no') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($post->isPublished())
                                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.blog.admin.view') }}</a>
                                    @endif
                                    <a href="{{ route('admin.blog.edit', $post) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900">{{ __('talenma.blog.admin.edit') }}</a>
                                    <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm(@js(__('talenma.blog.admin.delete_confirm')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">{{ __('talenma.blog.admin.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">{{ __('talenma.blog.admin.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $posts->links() }}
    </div>
</x-app-layout>
