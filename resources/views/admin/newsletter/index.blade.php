<x-admin::layouts>
    <x-slot:title>
        @lang('Newsletter Subscriptions')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Newsletter — Subscriptions')
        </p>
    </div>

    {!! view_render_event('bagisto.admin.newsletter.list.before') !!}

    <!-- Summary -->
    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">@lang('Total subscriptions')</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">{{ $total }}</p>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('admin.newsletter.index') }}" class="mt-5 flex flex-wrap items-end gap-2.5">
        <div class="w-80 max-sm:w-full">
            <x-admin::form.control-group.control
                type="text"
                name="search"
                :value="old('search', $search)"
                :label="trans('Search')"
                :placeholder="trans('Search by email...')"
            />
        </div>

        <button type="submit" class="secondary-button">
            @lang('Search')
        </button>

        @if ($search !== '')
            <a
                href="{{ route('admin.newsletter.index') }}"
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Subscribed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white">
                                    <a href="mailto:{{ $subscription->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $subscription->email }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $subscription->source ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $subscription->created_at ? $subscription->created_at->format('M d, Y H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <form
                                        method="POST"
                                        action="{{ route('admin.newsletter.destroy', $subscription->id) }}"
                                        onsubmit="return confirm('Delete the subscription for {{ $subscription->email }}?')"
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
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @lang('No subscriptions yet — the "Let\'s stay in touch!" form on the website saves them here.')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($subscriptions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.newsletter.list.after') !!}
</x-admin::layouts>
