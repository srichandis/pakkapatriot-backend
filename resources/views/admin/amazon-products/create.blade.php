<x-admin::layouts>
    <x-slot:title>
        @lang('Add Amazon Product')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Add Amazon Product')
        </p>

        <a href="{{ route('admin.amazon-products.index') }}" class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
            @lang('Back')
        </a>
    </div>

    <form method="POST" action="{{ route('admin.amazon-products.store') }}" class="mt-5 box-shadow rounded bg-white p-6 dark:bg-gray-900">
        @csrf

        @include('admin.amazon-products._form')

        <div class="mt-6 flex items-center gap-2.5">
            <button type="submit" class="primary-button">
                @lang('Save Product')
            </button>

            <a href="{{ route('admin.amazon-products.index') }}" class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
                @lang('Cancel')
            </a>
        </div>
    </form>
</x-admin::layouts>
