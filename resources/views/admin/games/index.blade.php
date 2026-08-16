<x-admin::layouts>
    <x-slot:title>
        @lang('Games')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Games of Bhārat')
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.games.create') }}"
                class="primary-button"
            >
                @lang('Create Game')
            </a>
        </div>
    </div>

    {!! view_render_event('bagisto.admin.games.list.before') !!}

    <!-- Search -->
    <form method="GET" action="{{ route('admin.games.index') }}" class="mt-5 flex flex-wrap items-end gap-2.5">
        <div class="w-64 max-sm:w-full">
            <x-admin::form.control-group.control
                type="text"
                name="search"
                :value="old('search', $search)"
                :label="trans('Search')"
                :placeholder="trans('Search by title, tagline or description...')"
            />
        </div>

        <button type="submit" class="secondary-button">
            @lang('Search')
        </button>

        @if ($search !== '')
            <a
                href="{{ route('admin.games.index') }}"
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Badge</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Path</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($games as $game)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-white">
                                    <div class="max-w-xs truncate font-medium">{{ $game->title }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $game->tagline ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $game->badge ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <code class="text-xs">{{ $game->path ?? '—' }}</code>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.games.edit', $game->id) }}"
                                            class="px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.games.destroy', $game->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this game?')"
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
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No games found.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($games->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $games->links() }}
                </div>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.games.list.after') !!}
</x-admin::layouts>
