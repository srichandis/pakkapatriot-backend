<x-admin::layouts>
    <x-slot:title>
        @lang('Journey Submissions')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Join the Journey — Submissions')
        </p>
    </div>

    {!! view_render_event('bagisto.admin.journey.list.before') !!}

    <!-- Summary -->
    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">@lang('Total submissions')</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">{{ $total }}</p>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('admin.journey.index') }}" class="mt-5 flex flex-wrap items-end gap-2.5">
        <div class="w-80 max-sm:w-full">
            <x-admin::form.control-group.control
                type="text"
                name="search"
                :value="old('search', $search)"
                :label="trans('Search')"
                :placeholder="trans('Search by name, email or city...')"
            />
        </div>

        <button type="submit" class="secondary-button">
            @lang('Search')
        </button>

        @if ($search !== '')
            <a
                href="{{ route('admin.journey.index') }}"
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Age</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">City</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Interests</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($submissions as $submission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $submission->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $submission->email }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $submission->age ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $submission->city ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if (empty($submission->interests))
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($submission->interests as $interest)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#FEF5E0] text-[#B45309] dark:bg-gray-800 dark:text-[#F6B828]">
                                                    {{ $interest }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $submission->created_at ? $submission->created_at->format('M d, Y H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <form
                                        method="POST"
                                        action="{{ route('admin.journey.destroy', $submission->id) }}"
                                        onsubmit="return confirm('Delete this submission from {{ $submission->name }}?')"
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No submissions yet — the "Join the Journey" form on the website saves them here.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($submissions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.journey.list.after') !!}
</x-admin::layouts>
