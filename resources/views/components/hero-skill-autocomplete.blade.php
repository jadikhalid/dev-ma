@props([
    'id' => 'hero-keyword',
    'name' => 'keyword',
    'value' => '',
    'placeholder' => '',
])

<div
    class="relative flex-1 min-w-0"
    x-data="heroSkillAutocomplete({
        url: @js(route('skill-suggestions')),
        initial: @js($value),
        loadingLabel: @js(__('talenma.home.search_loading')),
        emptyLabel: @js(__('talenma.home.search_no_results')),
    })"
    @click.outside="close()"
>
    <label for="{{ $id }}" class="sr-only">{{ __('talenma.home.search_skills') }}</label>
    <input
        id="{{ $id }}"
        type="text"
        name="{{ $name }}"
        x-model="query"
        x-ref="input"
        @input="onInput()"
        @keydown="onKeydown($event)"
        @focus="onFocus()"
        placeholder="{{ $placeholder }}"
        maxlength="128"
        autocomplete="off"
        role="combobox"
        aria-controls="{{ $id }}-listbox"
        :aria-expanded="open"
        aria-autocomplete="list"
        class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3"
    >

    <div
        x-show="open && (loading || suggestions.length > 0 || (query.trim() && !loading))"
        x-cloak
        class="absolute left-0 right-0 top-full z-50 mt-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
    >
        <p x-show="loading" class="px-3 py-2.5 text-sm text-gray-500" x-text="loadingLabel"></p>

        <ul
            x-show="!loading && suggestions.length > 0"
            id="{{ $id }}-listbox"
            role="listbox"
            class="max-h-56 overflow-y-auto py-1"
        >
            <template x-for="(item, index) in suggestions" :key="item.id">
                <li role="option" :aria-selected="index === activeIndex">
                    <button
                        type="button"
                        @click="select(item)"
                        class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm transition"
                        :class="index === activeIndex ? 'bg-indigo-50 text-indigo-700' : 'text-gray-800 hover:bg-gray-50'"
                    >
                        <span x-text="item.label" class="font-medium"></span>
                        <span x-text="item.profession" class="shrink-0 text-xs text-gray-400"></span>
                    </button>
                </li>
            </template>
        </ul>

        <p
            x-show="!loading && query.trim() && suggestions.length === 0"
            class="px-3 py-2.5 text-sm text-gray-500"
            x-text="emptyLabel"
        ></p>
    </div>
</div>
