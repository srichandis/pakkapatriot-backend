<?php

namespace App\Support;

/**
 * The knowledge collections (Ideas, Places, People, Culture, Create) and the
 * URLs their pages live at.
 *
 * Everything is under /{type} except Creations: React published only their
 * detail pages (at `/create/{slug}`, sharing the prefix with the maker's space),
 * so the creations index sits one level down at /create/creations rather than
 * stealing `/create` from the maker's space.
 */
class Collections
{
    /**
     * Every collection type that has a browse page.
     *
     * @var array<int, string>
     */
    public const TYPES = ['ideas', 'places', 'people', 'culture', 'create'];

    /**
     * Public browse path for a collection type.
     */
    public static function path(string $type): string
    {
        return $type === 'create' ? '/create/creations' : '/'.$type;
    }

    /**
     * Absolute URL of a collection's browse page.
     */
    public static function browseUrl(string $type): string
    {
        return url(self::path($type));
    }

    /**
     * Absolute URL of one item's detail page.
     */
    public static function itemUrl(string $type, string $slug): string
    {
        return url(self::path($type).'/'.$slug);
    }
}
