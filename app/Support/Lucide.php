<?php

namespace App\Support;

/**
 * Lucide icon paths.
 *
 * The React front-end rendered icons with lucide-react; the generated data file
 * at resources/icons/lucide.php carries the identical SVG children so the Blade
 * views draw the same glyphs. Database rows (collection items, game tags) store
 * the PascalCase React component name ("MapPin"), while views pass kebab-case
 * ("map-pin"), so both spellings resolve here.
 */
class Lucide
{
    /** @var array<string, string>|null */
    protected static ?array $paths = null;

    /**
     * Every icon, keyed by its kebab-case name.
     *
     * @return array<string, string>
     */
    public static function paths(): array
    {
        return self::$paths ??= require resource_path('icons/lucide.php');
    }

    /**
     * SVG children for an icon name, or an empty string when it is unknown.
     */
    public static function path(?string $name): string
    {
        if (! $name) {
            return '';
        }

        return self::paths()[self::slug($name)] ?? '';
    }

    /**
     * Whether an icon name resolves to a real glyph.
     */
    public static function has(?string $name): bool
    {
        return $name !== null && isset(self::paths()[self::slug($name)]);
    }

    /**
     * Normalise "MapPin" / "Music2" / "map-pin" to the icon file's slug.
     */
    public static function slug(string $name): string
    {
        $slug = preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $name);
        $slug = preg_replace('/([A-Za-z])(\d+)$/', '$1-$2', (string) $slug);

        return strtolower((string) $slug);
    }
}
