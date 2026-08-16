<x-admin::layouts>
    <x-slot:title>
        @lang('Create Game')
    </x-slot>

    <x-admin::form
        :action="route('admin.games.store')"
    >
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Create Game')
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.games.index') }}"
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

        @include('admin.games._form', [
            'icons' => $icons,
        ])
    </x-admin::form>
</x-admin::layouts>
