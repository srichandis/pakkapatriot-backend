<x-admin::layouts>
    <x-slot:title>
        @lang('Create Blog Post')
    </x-slot>

    <x-admin::form
        :action="route('admin.blogs.store')"
        enctype="multipart/form-data"
    >
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('Create Blog Post')
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.blogs.index') }}"
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

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Column -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <!-- Content -->
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('Blog Content')
                    </p>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('Title')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="title"
                            name="title"
                            rules="required"
                            :value="old('title')"
                            :label="trans('Title')"
                            :placeholder="trans('Enter blog post title')"
                        />

                        <x-admin::form.control-group.error control-name="title" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('Slug')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            id="slug"
                            name="slug"
                            :value="old('slug')"
                            :label="trans('Slug')"
                            :placeholder="trans('Leave empty to auto-generate from title')"
                        />

                        <x-admin::form.control-group.error control-name="slug" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('Excerpt')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="excerpt"
                            name="excerpt"
                            :value="old('excerpt')"
                            :label="trans('Excerpt')"
                            :placeholder="trans('Short summary of the blog post')"
                            rows="3"
                        />

                        <x-admin::form.control-group.error control-name="excerpt" />
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">
                            @lang('Content')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            id="content"
                            name="content"
                            rules="required"
                            :value="old('content')"
                            :label="trans('Content')"
                            :placeholder="trans('Write your blog post content here...')"
                            :tinymce="true"
                        />

                        <x-admin::form.control-group.error control-name="content" />
                    </x-admin::form.control-group>
                </div>
            </div>

            <!-- Right Column -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('Publishing')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('Author Name')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                id="author_name"
                                name="author_name"
                                :value="old('author_name', auth()->guard('admin')->user()->name ?? '')"
                                :label="trans('Author Name')"
                                :placeholder="trans('Author name')"
                            />

                            <x-admin::form.control-group.error control-name="author_name" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('Published At')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="datetime"
                                id="published_at"
                                name="published_at"
                                :value="old('published_at')"
                                :label="trans('Published At')"
                                :placeholder="trans('Select date and time')"
                            />

                            <x-admin::form.control-group.error control-name="published_at" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    id="is_published"
                                    name="is_published"
                                    :value="1"
                                    :checked="old('is_published')"
                                    :label="trans('Published')"
                                />
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    @lang('Publish immediately')
                                </span>
                            </label>
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::accordion>

                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('Featured Image')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>
                                @lang('Featured Image')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="file"
                                id="featured_image"
                                name="featured_image"
                                accept="image/*"
                                :label="trans('Featured Image')"
                            />

                            <x-admin::form.control-group.error control-name="featured_image" />
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::accordion>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
