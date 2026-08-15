<x-admin::layouts>
    <x-slot:title>
        @lang('People')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('People of Bhārat')
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.people.create') }}"
                class="primary-button"
            >
                @lang('Create Person')
            </a>
        </div>
    </div>

    {!! view_render_event('bagisto.admin.people.list.before') !!}

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.people.index') }}" class="mt-5 flex flex-wrap items-end gap-2.5">
        <div class="w-64 max-sm:w-full">
            <x-admin::form.control-group.control
                type="text"
                name="search"
                :value="old('search', $search)"
                :label="trans('Search')"
                :placeholder="trans('Search by name, region or tagline...')"
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
                    <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                @endforeach
            </x-admin::form.control-group.control>
        </div>

        <button type="submit" class="secondary-button">
            @lang('Filter')
        </button>

        @if ($search !== '' || $category)
            <a
                href="{{ route('admin.people.index') }}"
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Era</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Region</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($people as $person)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-white">
                                    <div class="max-w-xs truncate font-medium">{{ $person->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $person->native_name ? $person->native_name . ' · ' : '' }}/{{ $person->slug }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $person->category ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $person->era ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $person->region ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.people.edit', $person->id) }}"
                                            class="px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.people.destroy', $person->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this person?')"
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
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No people found.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($people->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $people->links() }}
                </div>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.people.list.after') !!}
</x-admin::layouts>
