@props([
    'name',
    'size' => 20,
    'fill' => 'none',
    'stroke' => 2,
])

@php
    /**
     * Lucide icon — the same glyphs the React front-end drew with lucide-react.
     *
     * Paths live in resources/icons/lucide.php (generated from lucide-react) and
     * are resolved through App\Support\Lucide, which also accepts the PascalCase
     * component names stored in the database.
     */
    $body = \App\Support\Lucide::path($name);
@endphp

<svg
    {{ $attributes->merge([
        'width' => $size,
        'height' => $size,
        'viewBox' => '0 0 24 24',
        'fill' => $fill,
        'stroke' => 'currentColor',
        'stroke-width' => $stroke,
        'stroke-linecap' => 'round',
        'stroke-linejoin' => 'round',
        'aria-hidden' => 'true',
        'class' => 'shrink-0',
    ]) }}
>
    {!! $body !!}
</svg>
