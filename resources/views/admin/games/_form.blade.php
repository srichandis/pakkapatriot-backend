{{-- Shared form fields for creating/editing a game. --}}
@php
    $item ??= null;
    $val = fn (string $field, $fallback = null) => old($field, $item?->{$field} ?? $fallback);
    $tagsText = '';
    if (is_array($item?->tags)) {
        $lines = [];
        foreach ($item->tags as $tag) {
            $lines[] = isset($tag['icon']) && $tag['icon'] ? $tag['icon'] . '|' . $tag['label'] : ($tag['label'] ?? '');
        }
        $tagsText = implode("\n", $lines);
    }
@endphp

<div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
    <!-- Left Column -->
    <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
        <!-- Identity -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('Identity')
            </p>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required">
                    @lang('Title')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="title"
                    name="title"
                    rules="required"
                    :value="$val('title')"
                    :label="trans('Title')"
                    :placeholder="trans('e.g. Pachisi')"
                />

                <x-admin::form.control-group.error control-name="title" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Tagline')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="tagline"
                    name="tagline"
                    :value="$val('tagline')"
                    :label="trans('Tagline')"
                    :placeholder="trans('e.g. The Royal Game of India')"
                />

                <x-admin::form.control-group.error control-name="tagline" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Badge')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="badge"
                    name="badge"
                    :value="$val('badge')"
                    :label="trans('Badge')"
                    :placeholder="trans('e.g. ★ Mahabharata')"
                />

                <x-admin::form.control-group.error control-name="badge" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Play path')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="path"
                    name="path"
                    :value="$val('path')"
                    :label="trans('Path')"
                    :placeholder="trans('e.g. /play/pachisi')"
                />

                <x-admin::form.control-group.error control-name="path" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Description')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="description"
                    name="description"
                    :value="$val('description')"
                    :label="trans('Description')"
                    :placeholder="trans('A short description of the game')"
                    rows="4"
                />

                <x-admin::form.control-group.error control-name="description" />
            </x-admin::form.control-group>
        </div>
    </div>

    <!-- Right Column -->
    <div class="flex w-80 flex-col gap-2 max-xl:w-full">
        <!-- Appearance -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('Appearance')
            </p>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Cover image')
                </x-admin::form.control-group.label>

                @if ($item?->image_url)
                    <img
                        src="{{ $item->image_url }}"
                        alt="{{ $item->title }}"
                        class="mb-2.5 h-24 w-24 rounded-lg border border-gray-200 object-cover dark:border-gray-700"
                    />
                @endif

                <x-admin::form.control-group.control
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    :label="trans('Cover image')"
                />

                <x-admin::form.control-group.error control-name="image" />

                @if ($item?->image)
                    <div class="mt-2.5 flex items-center gap-x-2">
                        <input
                            type="checkbox"
                            id="remove_image"
                            name="remove_image"
                            value="1"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-700"
                            @checked(old('remove_image'))
                        />

                        <label
                            for="remove_image"
                            class="cursor-pointer text-xs text-gray-600 dark:text-gray-300"
                        >
                            @lang('Remove the current image')
                        </label>
                    </div>
                @endif
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Accent gradient')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="accent"
                    name="accent"
                    :value="$val('accent')"
                    :label="trans('Accent')"
                    :placeholder="trans('from-[#581C87] to-[#9333EA]')"
                />

                <x-admin::form.control-group.error control-name="accent" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Tags (one per line)')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="tags"
                    name="tags"
                    :value="old('tags', $tagsText)"
                    :label="trans('Tags')"
                    :placeholder="trans('Icon|Label  —  e.g. Dices|2–4 players' . PHP_EOL . 'or just a plain label')"
                    rows="7"
                />

                <x-admin::form.control-group.error control-name="tags" />
            </x-admin::form.control-group>
        </div>
    </div>
</div>
