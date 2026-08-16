<x-admin::layouts>
    <x-slot:title>
        @lang('Edit ' . ucfirst($meta['itemNounSingular'] ?? rtrim($type, 's')))
    </x-slot>

    <x-admin::form
        :action="route('admin.' . $type . '.update', $item->id)"
        method="PUT"
    >
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Edit ' . ucfirst($meta['itemNounSingular'] ?? rtrim($type, 's')))
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.' . $type . '.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.account.edit.back-btn')
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('Save')
                </button>
            </div>
        </div>

        @include('admin.collections._form', [
            'type' => $type,
            'meta' => $meta,
            'item' => $item,
            'categories' => $categories,
            'icons' => $icons,
            'accents' => $accents,
        ])
    </x-admin::form>
</x-admin::layouts>
