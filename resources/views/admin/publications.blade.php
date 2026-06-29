<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.publications.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.admin.publications.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        {{-- Actualités (bandeau haut) --}}
        <section id="actualites" class="scroll-mt-24 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.admin.news.title') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.news.subtitle', ['max' => $newsMaxItems]) }}</p>
            </div>

            @if (session('news_saved'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                    {{ __('talenma.admin.news.saved') }}
                </div>
            @endif

            @if (session('news_deleted'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm">
                    {{ __('talenma.admin.news.deleted') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border p-6 sm:p-8">
                <h4 class="font-semibold text-gray-900">{{ __('talenma.admin.news.add_title') }}</h4>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.news.add_hint', ['max' => $newsMaxItems]) }}</p>

                <form method="POST" action="{{ route('admin.publications.news.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="news_title" :value="__('talenma.admin.news.field_title')" />
                        <x-text-input id="news_title" name="title" class="mt-1 block w-full" :value="old('title')" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="news_subtitle" :value="__('talenma.admin.news.field_subtitle')" />
                        <x-text-input id="news_subtitle" name="subtitle" class="mt-1 block w-full" :value="old('subtitle')" required />
                        <x-input-error :messages="$errors->get('subtitle')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="news_url" :value="__('talenma.admin.news.field_url')" />
                        <x-text-input id="news_url" name="url" type="url" class="mt-1 block w-full" :value="old('url')" placeholder="https://" required />
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.url_hint') }}</p>
                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="news_thumbnail" :value="__('talenma.admin.news.field_thumbnail')" />
                        <input id="news_thumbnail" name="thumbnail" type="file" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.thumbnail_hint') }}</p>
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('talenma.admin.news.submit') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.admin.news.current_list') }}</h4>
                    <span class="text-xs font-medium text-gray-500">{{ $newsItems->count() }} / {{ $newsMaxItems }}</span>
                </div>

                @if ($newsItems->isEmpty())
                    <p class="p-6 text-sm text-gray-500">{{ __('talenma.admin.news.empty') }}</p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($newsItems as $item)
                            <li class="p-4 sm:p-5 flex flex-col sm:flex-row gap-4 sm:items-center">
                                <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-400 to-indigo-600 ring-1 ring-gray-200">
                                    @if ($item->thumbnailUrl())
                                        <img src="{{ $item->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400">{{ $item->created_at->translatedFormat('d M Y H:i') }}</p>
                                    <p class="mt-1 font-semibold text-gray-900 truncate">{{ $item->title }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $item->subtitle }}</p>
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-indigo-600 hover:text-indigo-800 truncate block mt-1">{{ $item->url }}</a>
                                </div>
                                <form method="POST" action="{{ route('admin.publications.news.destroy', $item) }}" onsubmit="return confirm(@js(__('talenma.admin.news.delete_confirm')))">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">{{ __('talenma.admin.news.delete') }}</x-danger-button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        <hr class="border-gray-200">

        {{-- Posts réseaux sociaux (slider accueil) --}}
        <section id="reseaux" class="scroll-mt-24 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.admin.social_posts.title') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.social_posts.subtitle', ['max' => $socialMaxItems]) }}</p>
            </div>

            @if (session('post_saved'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                    {{ __('talenma.admin.social_posts.saved') }}
                </div>
            @endif

            @if (session('post_deleted'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm">
                    {{ __('talenma.admin.social_posts.deleted') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border p-6 sm:p-8">
                <h4 class="font-semibold text-gray-900">{{ __('talenma.admin.social_posts.add_title') }}</h4>
                <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.social_posts.add_hint', ['max' => $socialMaxItems]) }}</p>

                <form method="POST" action="{{ route('admin.publications.social-posts.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="post_title" :value="__('talenma.admin.social_posts.field_title')" />
                        <x-text-input id="post_title" name="post_title" class="mt-1 block w-full" :value="old('post_title')" required />
                        <x-input-error :messages="$errors->get('post_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="post_subtitle" :value="__('talenma.admin.social_posts.field_subtitle')" />
                        <x-text-input id="post_subtitle" name="post_subtitle" class="mt-1 block w-full" :value="old('post_subtitle')" required />
                        <x-input-error :messages="$errors->get('post_subtitle')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="post_url" :value="__('talenma.admin.social_posts.field_url')" />
                        <x-text-input id="post_url" name="post_url" type="url" class="mt-1 block w-full" :value="old('post_url')" placeholder="https://" required />
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.social_posts.url_hint') }}</p>
                        <x-input-error :messages="$errors->get('post_url')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="post_network" :value="__('talenma.admin.social_posts.field_network')" />
                        <select id="post_network" name="post_network" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach ($networks as $network)
                                <option value="{{ $network }}" @selected(old('post_network', 'linkedin') === $network)>
                                    {{ __('talenma.social_feed.sources.'.$network) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('post_network')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="post_thumbnail" :value="__('talenma.admin.social_posts.field_thumbnail')" />
                        <input id="post_thumbnail" name="post_thumbnail" type="file" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.social_posts.thumbnail_hint') }}</p>
                        <x-input-error :messages="$errors->get('post_thumbnail')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('talenma.admin.social_posts.submit') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.admin.social_posts.current_list') }}</h4>
                    <span class="text-xs font-medium text-gray-500">{{ $socialPosts->count() }} / {{ $socialMaxItems }}</span>
                </div>

                @if ($socialPosts->isEmpty())
                    <p class="p-6 text-sm text-gray-500">{{ __('talenma.admin.social_posts.empty') }}</p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($socialPosts as $item)
                            <li class="p-4 sm:p-5 flex flex-col sm:flex-row gap-4 sm:items-center">
                                <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-indigo-400 to-indigo-600 ring-1 ring-gray-200">
                                    @if ($item->thumbnailUrl())
                                        <img src="{{ $item->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-600">
                                            {{ $item->localizedNetworkLabel() }}
                                        </span>
                                        <p class="text-xs text-gray-400">{{ $item->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                    <p class="mt-1 font-semibold text-gray-900 truncate">{{ $item->title }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ $item->subtitle }}</p>
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-indigo-600 hover:text-indigo-800 truncate block mt-1">{{ $item->url }}</a>
                                </div>
                                <form method="POST" action="{{ route('admin.publications.social-posts.destroy', $item) }}" onsubmit="return confirm(@js(__('talenma.admin.social_posts.delete_confirm')))">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">{{ __('talenma.admin.social_posts.delete') }}</x-danger-button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
