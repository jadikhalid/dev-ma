@php
    $isEdit = $newsletter->exists;
    $initialBlocks = old('body_blocks');
    if (is_string($initialBlocks)) {
        $decoded = json_decode($initialBlocks, true);
        $initialBlocks = is_array($decoded) ? $decoded : $newsletter->normalizedBlocks();
    } elseif (! is_array($initialBlocks)) {
        $initialBlocks = $newsletter->normalizedBlocks();
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $isEdit ? __('talenma.newsletter.edit_title') : __('talenma.newsletter.create_title') }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('talenma.newsletter.form_help', ['count' => $recipientCount]) }}</p>
            </div>
            <a href="{{ route('admin.newsletter.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                ← {{ __('talenma.newsletter.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form
            method="POST"
            action="{{ $isEdit ? route('admin.newsletter.update', $newsletter) : route('admin.newsletter.store') }}"
            class="space-y-5 rounded-2xl border bg-white p-6 sm:p-8"
            x-data="newsletterBuilder({
                blocks: @js($initialBlocks),
                picker: @js($picker),
                labels: @js([
                    'header' => __('talenma.newsletter.block_type_header'),
                    'hero' => __('talenma.newsletter.block_type_hero'),
                    'jobs' => __('talenma.newsletter.block_type_jobs'),
                    'blog' => __('talenma.newsletter.block_type_blog'),
                    'social' => __('talenma.newsletter.block_type_social'),
                    'talents' => __('talenma.newsletter.block_type_talents'),
                    'companies' => __('talenma.newsletter.block_type_companies'),
                    'stats' => __('talenma.newsletter.block_type_stats'),
                    'text' => __('talenma.newsletter.block_type_text'),
                    'cta' => __('talenma.newsletter.block_type_cta'),
                    'register' => __('talenma.newsletter.block_type_register'),
                    'library' => __('talenma.newsletter.block_type_library'),
                    'cv_templates' => __('talenma.newsletter.block_type_cv_templates'),
                    'add_block' => __('talenma.newsletter.add_block'),
                    'remove' => __('talenma.newsletter.remove_block'),
                    'up' => __('talenma.newsletter.move_up'),
                    'down' => __('talenma.newsletter.move_down'),
                    'heading' => __('talenma.newsletter.field_heading'),
                    'pick_items' => __('talenma.newsletter.pick_items'),
                    'pick_library' => __('talenma.newsletter.pick_library'),
                    'pick_cv_templates' => __('talenma.newsletter.pick_cv_templates'),
                    'cv_description' => __('talenma.newsletter.field_cv_template_description'),
                ]),
            })"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="body_blocks" :value="JSON.stringify(blocks)">

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-input-label for="title" :value="__('talenma.newsletter.field_title')" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $newsletter->title)" required maxlength="255" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="subject" :value="__('talenma.newsletter.field_subject')" />
                    <x-text-input id="subject" name="subject" class="mt-1 block w-full" :value="old('subject', $newsletter->subject)" required maxlength="255" />
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="headline" :value="__('talenma.newsletter.field_headline')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.newsletter.field_headline_help') }}</p>
                    <x-text-input
                        id="headline"
                        name="headline"
                        class="mt-1 block w-full"
                        :value="old('headline', $newsletter->headline)"
                        maxlength="255"
                        :placeholder="$defaultHeadline"
                    />
                    <x-input-error :messages="$errors->get('headline')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="locale" :value="__('talenma.newsletter.field_locale')" />
                    <select id="locale" name="locale" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">
                        <option value="fr" @selected(old('locale', $newsletter->locale) === 'fr')">FR</option>
                        <option value="en" @selected(old('locale', $newsletter->locale) === 'en')">EN</option>
                    </select>
                </div>
                <div class="sm:col-span-2" x-data="{ audience: @js(old('audience', $newsletter->audience ?: 'all')), counts: @js($recipientCounts) }">
                    <x-input-label :value="__('talenma.newsletter.field_audience')" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.newsletter.field_audience_help') }}</p>
                    <div class="mt-3 flex flex-col gap-2">
                        @foreach ([
                            'all' => __('talenma.newsletter.audience_all'),
                            'registered' => __('talenma.newsletter.audience_registered'),
                            'guests' => __('talenma.newsletter.audience_guests'),
                        ] as $value => $label)
                            <label class="flex items-start gap-2.5 rounded-lg border border-gray-200 px-3 py-2.5 hover:bg-gray-50 cursor-pointer">
                                <input
                                    type="radio"
                                    name="audience"
                                    value="{{ $value }}"
                                    class="mt-0.5 text-indigo-600"
                                    x-model="audience"
                                    @checked(old('audience', $newsletter->audience ?: 'all') === $value)
                                >
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-gray-900">{{ $label }}</span>
                                    <span class="block text-xs text-gray-500">
                                        {{ __('talenma.newsletter.audience_count', ['count' => $recipientCounts[$value] ?? 0]) }}
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm font-medium text-indigo-700">
                        <span x-text="(@js(__('talenma.newsletter.audience_selected_count'))).replace(':count', String(counts[audience] ?? 0))"></span>
                    </p>
                    <x-input-error :messages="$errors->get('audience')" class="mt-2" />
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-600">{{ __('talenma.newsletter.blocks_title') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="type in blockTypes" :key="type">
                            <button type="button" class="rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100" @click="addBlock(type)" x-text="labels.add_block + ': ' + (labels[type] || type)"></button>
                        </template>
                    </div>
                </div>

                <template x-for="(block, index) in blocks" :key="block._uid">
                    <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/50">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800" x-text="labels[block.type] || block.type"></p>
                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    class="text-xs px-2 py-1 rounded border bg-white disabled:opacity-40 disabled:cursor-not-allowed"
                                    :disabled="! canMoveUp(index)"
                                    :aria-label="labels.up"
                                    @click="moveUp(index)"
                                    x-text="labels.up"
                                ></button>
                                <button
                                    type="button"
                                    class="text-xs px-2 py-1 rounded border bg-white disabled:opacity-40 disabled:cursor-not-allowed"
                                    :disabled="! canMoveDown(index)"
                                    :aria-label="labels.down"
                                    @click="moveDown(index)"
                                    x-text="labels.down"
                                ></button>
                                <button type="button" class="text-xs px-2 py-1 rounded border border-rose-200 text-rose-700 bg-white" @click="removeBlock(index)" x-text="labels.remove"></button>
                            </div>
                        </div>

                        <template x-if="block.type === 'header'">
                            <div class="space-y-2">
                                <input type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.newsletter.field_header_title') }}" x-model="block.title">
                                <textarea rows="2" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.newsletter.field_header_subtitle') }}" x-model="block.subtitle"></textarea>
                            </div>
                        </template>

                        <template x-if="block.type === 'hero'">
                            <div class="space-y-2">
                                <input type="url" class="w-full rounded-lg border-gray-300 text-sm" placeholder="https://…" x-model="block.image_url">
                                <input type="text" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Alt" x-model="block.alt">
                            </div>
                        </template>

                        <template x-if="['jobs','blog','social','talents','companies','library'].includes(block.type)">
                            <div class="space-y-2">
                                <input type="text" class="w-full rounded-lg border-gray-300 text-sm" :placeholder="labels.heading" x-model="block.heading">
                                <p class="text-xs text-slate-500" x-text="block.type === 'library' ? labels.pick_library : labels.pick_items"></p>
                                <div class="max-h-40 overflow-y-auto space-y-1 rounded-lg border bg-white p-2">
                                    <template x-for="item in pickerFor(block.type)" :key="item.id">
                                        <label class="flex items-start gap-2 text-sm text-slate-700">
                                            <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600" :checked="isSelected(block, item.id)" @change="toggleId(block, item.id, $event.target.checked)">
                                            <span x-text="item.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="block.type === 'cv_templates'">
                            <div class="space-y-3" x-init="ensureCvDescriptions(block)">
                                <input type="text" class="w-full rounded-lg border-gray-300 text-sm" :placeholder="labels.heading" x-model="block.heading">
                                <p class="text-xs text-slate-500" x-text="labels.pick_cv_templates"></p>
                                <div class="max-h-40 overflow-y-auto space-y-1 rounded-lg border bg-white p-2">
                                    <template x-for="item in pickerFor(block.type)" :key="item.id">
                                        <label class="flex items-start gap-2 text-sm text-slate-700">
                                            <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600" :checked="isSelected(block, item.id)" @change="toggleId(block, item.id, $event.target.checked)">
                                            <span x-text="item.label"></span>
                                        </label>
                                    </template>
                                </div>
                                <template x-for="key in selectedIds(block)" :key="key">
                                    <div class="rounded-lg border bg-white p-3 space-y-1.5">
                                        <p class="text-xs font-semibold text-slate-700" x-text="cvTemplateLabel(key)"></p>
                                        <textarea rows="3" class="w-full rounded-lg border-gray-300 text-sm" :placeholder="labels.cv_description" x-model="ensureCvDescriptions(block)[key]"></textarea>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="block.type === 'stats'">
                            <div class="space-y-2">
                                <template x-for="(item, si) in block.items" :key="si">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" class="rounded-lg border-gray-300 text-sm" placeholder="12" x-model="item.value">
                                        <input type="text" class="rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.newsletter.field_stat_label') }}" x-model="item.label">
                                    </div>
                                </template>
                                <button type="button" class="text-xs font-semibold text-indigo-600" @click="block.items.push({value:'',label:''})">+ {{ __('talenma.newsletter.add_stat') }}</button>
                            </div>
                        </template>

                        <template x-if="block.type === 'text'">
                            <textarea rows="4" class="w-full rounded-lg border-gray-300 text-sm" x-model="block.body" placeholder="{{ __('talenma.newsletter.field_text_body') }}"></textarea>
                        </template>

                        <template x-if="block.type === 'cta'">
                            <div class="grid sm:grid-cols-2 gap-2">
                                <input type="text" class="rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.newsletter.field_cta_label') }}" x-model="block.label">
                                <input type="url" class="rounded-lg border-gray-300 text-sm" placeholder="https://…" x-model="block.url">
                            </div>
                        </template>

                        <template x-if="block.type === 'register'">
                            <p class="text-sm text-slate-600">{{ __('talenma.newsletter.register_banner_help') }}</p>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex flex-wrap justify-end gap-3 pt-2">
                <a href="{{ route('admin.newsletter.index') }}" class="inline-flex px-4 py-2.5 border rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('talenma.newsletter.cancel') }}</a>
                <x-primary-button type="submit">{{ __('talenma.newsletter.save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
