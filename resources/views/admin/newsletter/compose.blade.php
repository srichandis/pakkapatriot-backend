<x-admin::layouts>
    <x-slot:title>
        @lang('Compose Newsletter')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('Compose Newsletter')
        </p>
    </div>

    <!-- Summary -->
    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="text-xs font-medium text-gray-500 uppercase dark:text-gray-400">@lang('Recipients')</p>
            <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white">{{ $total }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">@lang('subscribers will receive this email')</p>
        </div>
    </div>

    @if (session('error'))
        <div class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- Compose Form -->
    <form method="POST" action="{{ route('admin.newsletter.send') }}" class="mt-5" id="newsletter-form">
        @csrf

        <div class="box-shadow rounded bg-white p-6 dark:bg-gray-900">
            <!-- Subject -->
            <div class="mb-5">
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('Subject')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="subject"
                        :value="old('subject')"
                        :label="trans('Subject')"
                        :placeholder="trans('e.g. Discover the People of Bhārat')"
                        rules="required"
                    />

                    @error('subject')
                        <x-admin::form.control-group.error>
                            {{ $message }}
                        </x-admin::form.control-group.error>
                    @enderror
                </x-admin::form.control-group>
            </div>

            <!-- Body -->
            <div class="mb-5">
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('Email Body')
                    </x-admin::form.control-group.label>

                    <p class="mb-2 text-xs text-gray-400 dark:text-gray-500">
                        @lang('You can use HTML for formatting. The email will be wrapped in a branded template.')
                    </p>

                    <textarea
                        name="body"
                        rows="16"
                        class="w-full rounded-md border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800
                               transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                               dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400
                               dark:focus:ring-blue-900"
                        placeholder="{{ trans('Write your newsletter content here...') }}"
                        required
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <x-admin::form.control-group.error>
                            {{ $message }}
                        </x-admin::form.control-group.error>
                    @enderror
                </x-admin::form.control-group>
            </div>

            <!-- Preview -->
            <div class="mb-5">
                <p class="mb-2 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">@lang('Preview')</p>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div id="preview-area" class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300">
                        <p class="text-gray-400 italic">@lang('Type in the body field above to see a preview...')</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="primary-button"
                    onclick="return confirm('Send this newsletter to {{ $total }} subscriber(s)?')"
                >
                    <span class="icon-send text-sm"></span>
                    @lang('Send Newsletter')
                </button>

                <a
                    href="{{ route('admin.newsletter.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('Cancel')
                </a>
            </div>
        </div>
    </form>

    <!-- Live preview script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.querySelector('textarea[name="body"]');
            const preview = document.getElementById('preview-area');

            function updatePreview() {
                const val = textarea.value.trim();
                if (val) {
                    preview.innerHTML = val;
                } else {
                    preview.innerHTML = '<p class="text-gray-400 italic">Type in the body field above to see a preview...</p>';
                }
            }

            textarea.addEventListener('input', updatePreview);
        });
    </script>
</x-admin::layouts>
