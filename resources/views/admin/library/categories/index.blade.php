<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.library.categories_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.admin.library.categories_subtitle') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.library.books.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    {{ __('talenma.admin.library.nav_books') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('talenma.admin.library.add_category') }}</h3>
            <form method="POST" action="{{ route('admin.library.categories.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.name_fr') }}</label>
                    <input type="text" name="name_fr" value="{{ old('name_fr') }}" required class="w-full rounded-xl border-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.name_en') }}</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required class="w-full rounded-xl border-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.parent') }}</label>
                    <select name="parent_id" class="w-full rounded-xl border-gray-200 text-sm">
                        <option value="">{{ __('talenma.admin.library.parent_root') }}</option>
                        @foreach ($parentOptions as $option)
                            <option value="{{ $option['id'] }}" @selected(old('parent_id') == $option['id'])>
                                {{ $option['label'] }} (L{{ $option['depth'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.slug') }}</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-xl border-gray-200 text-sm" placeholder="auto">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.sort_order') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full rounded-xl border-gray-200 text-sm">
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_active', true))>
                        {{ __('talenma.admin.library.is_active') }}
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <button type="submit" class="inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        {{ __('talenma.admin.library.create_category') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_name') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_depth') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_counts') }}</th>
                        <th class="px-4 py-3">{{ __('talenma.admin.library.col_status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('talenma.admin.library.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($categories as $category)
                        <tr class="hover:bg-gray-50/80 align-top">
                            <td class="px-4 py-3" style="padding-left: {{ 1 + (($category->depth - 1) * 1.25) }}rem">
                                <p class="font-semibold text-gray-900">{{ $category->name_fr }}</p>
                                <p class="text-xs text-gray-400">{{ $category->name_en }} · {{ $category->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">L{{ $category->depth }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $category->children_count }} {{ __('talenma.admin.library.children') }}
                                · {{ $category->books_count }} {{ __('talenma.admin.library.books_short') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($category->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ __('talenma.admin.library.active') }}</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-600">{{ __('talenma.admin.library.inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <details class="text-right">
                                    <summary class="cursor-pointer text-xs font-semibold text-indigo-600 list-none">{{ __('talenma.admin.library.edit') }}</summary>
                                    <form method="POST" action="{{ route('admin.library.categories.update', $category) }}" class="mt-3 space-y-2 text-left rounded-xl border border-gray-100 bg-gray-50 p-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name_fr" value="{{ $category->name_fr }}" required class="w-full rounded-lg border-gray-200 text-xs">
                                        <input type="text" name="name_en" value="{{ $category->name_en }}" required class="w-full rounded-lg border-gray-200 text-xs">
                                        <input type="text" name="slug" value="{{ $category->slug }}" class="w-full rounded-lg border-gray-200 text-xs">
                                        <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-full rounded-lg border-gray-200 text-xs">
                                        <label class="inline-flex items-center gap-2 text-xs text-gray-700">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600" @checked($category->is_active)>
                                            {{ __('talenma.admin.library.is_active') }}
                                        </label>
                                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white">{{ __('talenma.admin.library.save') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.library.categories.destroy', $category) }}" class="mt-2" onsubmit="return confirm(@js(__('talenma.admin.library.delete_category_confirm')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">{{ __('talenma.admin.library.delete') }}</button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
