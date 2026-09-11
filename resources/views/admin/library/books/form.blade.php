<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $book->exists ? __('talenma.admin.library.edit_book') : __('talenma.admin.library.upload_book') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('talenma.admin.library.book_form_hint') }}</p>
            </div>
            <a href="{{ route('admin.library.books.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                {{ __('talenma.admin.library.back_books') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                <p class="font-semibold">{{ __('talenma.admin.library.form_errors_title') }}</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ $book->exists ? route('admin.library.books.update', $book) : route('admin.library.books.store') }}"
            enctype="multipart/form-data"
            class="rounded-2xl border border-gray-100 bg-white p-6 space-y-4 shadow-sm"
        >
            @csrf
            @if ($book->exists)
                @method('PUT')
            @endif

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.book_title') }}</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full rounded-xl border-gray-200 text-sm">
                @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.book_author') }}</label>
                <input type="text" name="author" value="{{ old('author', $book->author) }}" class="w-full rounded-xl border-gray-200 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.book_category') }}</label>
                <select name="library_category_id" required class="w-full rounded-xl border-gray-200 text-sm">
                    <option value="">{{ __('talenma.admin.library.choose_leaf') }}</option>
                    @foreach ($leafOptions as $option)
                        <option value="{{ $option['id'] }}" @selected(old('library_category_id', $book->library_category_id) == $option['id'])>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('library_category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">{{ __('talenma.admin.library.book_description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-xl border-gray-200 text-sm">{{ old('description', $book->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">
                    {{ $book->exists ? __('talenma.admin.library.replace_cover') : __('talenma.admin.library.cover_file') }}
                </label>
                <input type="file" name="cover" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" @required(! $book->exists) class="w-full text-sm">
                <p class="mt-1 text-xs text-gray-400">{{ __('talenma.admin.library.cover_hint') }}</p>
                @if ($book->exists && $book->coverUrl())
                    <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" class="mt-3 h-36 w-auto rounded-lg border border-gray-100 object-cover">
                @endif
                @error('cover') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">
                    {{ $book->exists ? __('talenma.admin.library.replace_pdf') : __('talenma.admin.library.pdf_file') }}
                </label>
                <input type="file" name="file" accept="application/pdf,.pdf" @required(! $book->exists) class="w-full text-sm">
                <p class="mt-1 text-xs text-gray-400">{{ __('talenma.admin.library.pdf_hint') }}</p>
                @if ($book->exists)
                    <p class="mt-1 text-xs text-gray-400">{{ $book->original_filename }} · {{ $book->formattedSize() }}</p>
                @endif
                @error('file') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300 text-indigo-600" @checked(old('is_published', $book->is_published))>
                {{ __('talenma.admin.library.publish') }}
            </label>

            <div class="pt-2">
                <button type="submit" class="inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    {{ __('talenma.admin.library.save') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
