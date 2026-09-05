@php
    $isEdit = $post->exists;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $isEdit ? __('talenma.blog.admin.edit_title') : __('talenma.blog.admin.create_title') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('talenma.blog.admin.form_help') }}</p>
            </div>
            <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                ← {{ __('talenma.blog.admin.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form
            method="POST"
            enctype="multipart/form-data"
            action="{{ $isEdit ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
            class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 sm:p-8"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_title') }}</label>
                <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required class="mt-1.5 w-full rounded-xl border-gray-300 text-sm">
                @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_slug') }}</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug) }}" class="mt-1.5 w-full rounded-xl border-gray-300 text-sm" placeholder="{{ __('talenma.blog.admin.slug_placeholder') }}">
                @error('slug') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_excerpt') }}</label>
                <textarea id="excerpt" name="excerpt" rows="3" required class="mt-1.5 w-full rounded-xl border-gray-300 text-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="body" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_body') }}</label>
                <textarea id="body" name="body" rows="14" required class="mt-1.5 w-full rounded-xl border-gray-300 text-sm font-mono">{{ old('body', $post->body) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.blog.admin.body_hint') }}</p>
                @error('body') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="locale" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_locale') }}</label>
                    <select id="locale" name="locale" class="mt-1.5 w-full rounded-xl border-gray-300 text-sm">
                        <option value="fr" @selected(old('locale', $post->locale) === 'fr')>FR</option>
                        <option value="en" @selected(old('locale', $post->locale) === 'en')>EN</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_status') }}</label>
                    <select id="status" name="status" class="mt-1.5 w-full rounded-xl border-gray-300 text-sm">
                        <option value="draft" @selected(old('status', $post->status) === 'draft')>{{ __('talenma.blog.admin.status_draft') }}</option>
                        <option value="published" @selected(old('status', $post->status) === 'published')>{{ __('talenma.blog.admin.status_published') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="cover" class="block text-sm font-semibold text-gray-800">{{ __('talenma.blog.admin.field_cover') }}</label>
                @if ($post->coverUrl())
                    <div class="mt-2 mb-2 overflow-hidden rounded-xl border border-gray-100">
                        <img src="{{ $post->coverUrl() }}" alt="" class="h-40 w-full object-cover">
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remove_cover" value="1" class="rounded border-gray-300 text-indigo-600">
                        {{ __('talenma.blog.admin.remove_cover') }}
                    </label>
                @endif
                <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                @error('cover') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <label class="inline-flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50/50 px-4 py-3">
                <input type="hidden" name="show_in_ticker" value="0">
                <input
                    type="checkbox"
                    name="show_in_ticker"
                    value="1"
                    class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    @checked(old('show_in_ticker', $post->show_in_ticker))
                >
                <span>
                    <span class="block text-sm font-semibold text-gray-900">{{ __('talenma.blog.admin.field_ticker') }}</span>
                    <span class="block text-xs text-gray-600 mt-0.5">{{ __('talenma.blog.admin.ticker_help') }}</span>
                </span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">{{ __('talenma.common.cancel') }}</a>
                <button type="submit" class="inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    {{ __('talenma.common.save') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
