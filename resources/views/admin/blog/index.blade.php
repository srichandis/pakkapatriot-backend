<x-admin::layouts>
    <x-slot:title>
        @lang('Blog Posts')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Blog Posts')
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.blogs.create') }}"
                class="primary-button"
            >
                @lang('Create Blog Post')
            </a>
        </div>
    </div>

    {!! view_render_event('bagisto.admin.blog.list.before') !!}

    <div class="mt-5">
        <div class="box-shadow rounded bg-white dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Published</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse($blogs as $blog)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-white">
                                    <div class="max-w-xs truncate font-medium">{{ $blog->title }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">/blog/{{ $blog->slug }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $blog->author_name ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($blog->is_published)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $blog->published_at ? $blog->published_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.blogs.edit', $blog->id) }}"
                                            class="px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.blogs.destroy', $blog->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this blog post?')"
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
                                    No blog posts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $blogs->onEachSide(1)->links('pagination::tailwind') }}
        </div>
    </div>

    {!! view_render_event('bagisto.admin.blog.list.after') !!}

</x-admin::layouts>
