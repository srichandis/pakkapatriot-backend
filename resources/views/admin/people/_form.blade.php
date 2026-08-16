{{-- Shared form fields for creating/editing a person. --}}
@php
    $item ??= null;
    $val = fn (string $field, $fallback = null) => old($field, $item?->{$field} ?? $fallback);
    // Render a JSON array field as textarea lines. Core ideas may be either
    // plain strings or objects with {title, text} — objects render as "Title|Text".
    $arr = function (string $field) use ($item) {
        $value = $item?->{$field};
        if (! is_array($value)) {
            return $value ?? '';
        }
        $lines = [];
        foreach ($value as $entry) {
            if (is_array($entry)) {
                $lines[] = trim(($entry['title'] ?? '') . '|' . ($entry['text'] ?? ''), '|');
            } else {
                $lines[] = (string) $entry;
            }
        }
        return implode("\n", $lines);
    };
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
                    @lang('Name')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="name"
                    name="name"
                    rules="required"
                    :value="$val('name')"
                    :label="trans('Name')"
                    :placeholder="trans('e.g. Mahatma Gandhi')"
                />

                <x-admin::form.control-group.error control-name="name" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Slug')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="slug"
                    name="slug"
                    :value="$val('slug')"
                    :label="trans('Slug')"
                    :placeholder="trans('Leave empty to auto-generate from name')"
                />

                <x-admin::form.control-group.error control-name="slug" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Native name')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="native_name"
                    name="native_name"
                    :value="$val('native_name')"
                    :label="trans('Native name')"
                    :placeholder="trans('e.g. महात्मा गांधी')"
                />

                <x-admin::form.control-group.error control-name="native_name" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Category')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    id="category"
                    name="category"
                    :value="$val('category')"
                    :label="trans('Category')"
                >
                    <option value="">@lang('— Select a category —')</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['id'] }}">
                            {{ $cat['label'] }}
                        </option>
                    @endforeach
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="category" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Era')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="era"
                    name="era"
                    :value="$val('era')"
                    :label="trans('Era')"
                    :placeholder="trans('e.g. 1869 – 1948')"
                />

                <x-admin::form.control-group.error control-name="era" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Attribution / Known as')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="attribution"
                    name="attribution"
                    :value="$val('attribution')"
                    :label="trans('Attribution')"
                    :placeholder="trans('e.g. Mohandas Karamchand Gandhi')"
                />

                <x-admin::form.control-group.error control-name="attribution" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Region / Birthplace')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="region"
                    name="region"
                    :value="$val('region')"
                    :label="trans('Region')"
                    :placeholder="trans('e.g. Porbandar, Gujarat')"
                />

                <x-admin::form.control-group.error control-name="region" />
            </x-admin::form.control-group>
        </div>

        <!-- Quote & Summary -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('Quote & Summary')
            </p>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Quote')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="quote"
                    name="quote"
                    :value="$val('quote')"
                    :label="trans('Quote')"
                    :placeholder="trans('A short quote attributed to this person')"
                />

                <x-admin::form.control-group.error control-name="quote" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Quote source')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="quote_source"
                    name="quote_source"
                    :value="$val('quote_source')"
                    :label="trans('Quote source')"
                    :placeholder="trans('Who said it / where it is from')"
                />

                <x-admin::form.control-group.error control-name="quote_source" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Summary')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="summary"
                    name="summary"
                    :value="$val('summary')"
                    :label="trans('Summary')"
                    :placeholder="trans('A short paragraph introducing this person')"
                    rows="4"
                />

                <x-admin::form.control-group.error control-name="summary" />
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
                    @lang('Icon')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    id="icon"
                    name="icon"
                    :value="$val('icon')"
                    :label="trans('Icon')"
                >
                    <option value="">@lang('— Select an icon —')</option>
                    @foreach ($icons as $icon)
                        <option value="{{ $icon }}">{{ $icon }}</option>
                    @endforeach
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="icon" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Accent gradient')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="accent"
                    name="accent"
                    list="people-accent-presets"
                    :value="$val('accent')"
                    :label="trans('Accent')"
                    :placeholder="trans('from-[#581C87] to-[#9333EA]')"
                />

                <datalist id="people-accent-presets">
                    @foreach ($accents as $accent)
                        <option value="{{ $accent }}"></option>
                    @endforeach
                </datalist>

                <x-admin::form.control-group.error control-name="accent" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Soft accent classes')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="soft_accent"
                    name="soft_accent"
                    :value="$val('soft_accent')"
                    :label="trans('Soft accent')"
                    :placeholder="trans('bg-purple-50 text-purple-700 border-purple-200')"
                />

                <x-admin::form.control-group.error control-name="soft_accent" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Icon colour classes')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    id="icon_color"
                    name="icon_color"
                    :value="$val('icon_color')"
                    :label="trans('Icon colour')"
                    :placeholder="trans('text-purple-600')"
                />

                <x-admin::form.control-group.error control-name="icon_color" />
            </x-admin::form.control-group>
        </div>

        <!-- Story -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('Story')
            </p>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Overview (one paragraph per line)')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="overview"
                    name="overview"
                    :value="$arr('overview')"
                    :label="trans('Overview')"
                    :placeholder="trans('Each line becomes one paragraph')"
                    rows="8"
                />

                <x-admin::form.control-group.error control-name="overview" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Core ideas (one per line)')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="core_ideas"
                    name="core_ideas"
                    :value="$arr('core_ideas')"
                    :label="trans('Core ideas')"
                    :placeholder="trans('Each line becomes a card. Use Title|Text for a titled card.')"
                    rows="6"
                />

                <x-admin::form.control-group.error control-name="core_ideas" />
            </x-admin::form.control-group>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label>
                    @lang('Legacy')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="textarea"
                    id="legacy"
                    name="legacy"
                    :value="$val('legacy')"
                    :label="trans('Legacy')"
                    :placeholder="trans('Why this person matters today')"
                    rows="4"
                />

                <x-admin::form.control-group.error control-name="legacy" />
            </x-admin::form.control-group>
        </div>
    </div>
</div>
