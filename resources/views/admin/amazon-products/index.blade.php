<x-admin::layouts>
    <x-slot:title>
        @lang('Amazon Products')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Made in Bhārat — Amazon Products')
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.amazon-products.create') }}"
                class="primary-button"
            >
                @lang('Add Product')
            </a>
        </div>
    </div>

    {!! view_render_event('bagisto.admin.amazon-products.list.before') !!}

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.amazon-products.index') }}" class="mt-5 flex flex-wrap items-end gap-2.5">
        <div class="w-64 max-sm:w-full">
            <x-admin::form.control-group.control
                type="text"
                name="search"
                :value="old('search', $search)"
                :label="trans('Search')"
                :placeholder="trans('Search by name, ASIN or category...')"
            />
        </div>

        <div class="w-56 max-sm:w-full">
            <x-admin::form.control-group.control
                type="select"
                name="category"
                :value="old('category', $category)"
                :label="trans('Category')"
            >
                <option value="">@lang('All categories')</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </x-admin::form.control-group.control>
        </div>

        <button type="submit" class="secondary-button">
            @lang('Filter')
        </button>

        @if ($search !== '' || $category)
            <a
                href="{{ route('admin.amazon-products.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('Clear')
            </a>
        @endif
    </form>

    <div class="mt-5">
        <div class="box-shadow rounded bg-white dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">ASIN</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Rating</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Active</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-white">
                                    <div class="max-w-xs truncate font-medium">{{ $product->name }}</div>
                                    @if ($product->image_url)
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <a href="{{ $product->link }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                @lang('View on Amazon') ↗
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $product->category }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $product->asin }}</code>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $product->price ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $product->rating !== null ? $product->rating . ' ★ (' . number_format($product->ratings_count ?? 0) . ')' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($product->active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.amazon-products.edit', $product->id) }}"
                                            class="px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.amazon-products.destroy', $product->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this product?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No products found. Add your first made-in-India Amazon product!')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.amazon-products.list.after') !!}
</x-admin::layouts>
