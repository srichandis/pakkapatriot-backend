@php
    $product = $product ?? null;
@endphp

<x-admin::form.control-group.control
    type="text"
    name="name"
    :value="old('name', $product?->name)"
    :label="trans('Product Name')"
    :placeholder="trans('e.g. Khadi Cotton Kurta for Men')"
    :required="true"
/>

<x-admin::form.control-group.control
    type="text"
    name="category"
    :value="old('category', $product?->category)"
    :label="trans('Category (accordion heading)')"
    :placeholder="trans('e.g. Traditional Wear')"
    :required="true"
/>

<x-admin::form.control-group.control
    type="textarea"
    name="description"
    :value="old('description', $product?->description)"
    :label="trans('Short Description')"
    :placeholder="trans('One or two sentences shown under the product name.')"
/>

<x-admin::form.control-group.control
    type="text"
    name="asin"
    :value="old('asin', $product?->asin)"
    :label="trans('Amazon ASIN')"
    :placeholder="trans('e.g. B0ABCDEFGH — the product code from the Amazon.in URL')"
    :required="true"
/>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <x-admin::form.control-group.control
        type="text"
        name="price"
        :value="old('price', $product?->price)"
        :label="trans('Price (display)')"
        :placeholder="trans('e.g. ₹1,299')"
    />

    <x-admin::form.control-group.control
        type="text"
        name="rating"
        :value="old('rating', $product?->rating)"
        :label="trans('Rating (0–5)')"
        :placeholder="trans('e.g. 4.3')"
    />

    <x-admin::form.control-group.control
        type="text"
        name="ratings_count"
        :value="old('ratings_count', $product?->ratings_count)"
        :label="trans('Ratings Count')"
        :placeholder="trans('e.g. 12500')"
    />
</div>

<x-admin::form.control-group.control
    type="text"
    name="image_url"
    :value="old('image_url', $product?->image_url)"
    :label="trans('Image URL')"
    :placeholder="trans('https://m.media-amazon.com/images/I/...jpg')"
/>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-admin::form.control-group.control
        type="text"
        name="sort_order"
        :value="old('sort_order', $product?->sort_order ?? 0)"
        :label="trans('Sort Order (lower = first)')"
    />

    <div class="mb-2.5">
        <x-admin::form.control-group.label>
            @lang('Visible on website')
        </x-admin::form.control-group.label>

        <label class="flex items-center gap-2">
            <input
                type="checkbox"
                name="active"
                value="1"
                {{ old('active', $product?->active ?? true) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="text-sm text-gray-600 dark:text-gray-300">@lang('Show this product in the Made in Bhārat accordion')</span>
        </label>
    </div>
</div>

<p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
    @lang('The affiliate link is built automatically as') <code class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">https://www.amazon.in/dp/&lt;ASIN&gt;?tag={{ config('services.amazon.affiliate_tag') }}</code>
</p>
