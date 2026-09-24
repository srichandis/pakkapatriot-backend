<?php

namespace App\Support;

/**
 * The catalogue stores accents as Tailwind class strings (e.g.
 * "from-[#AABBCC] to-[#DDEEFF]"). Tailwind can't compile classes that only
 * exist in database rows, so flip them into an inline gradient instead.
 */
class Gradient
{
    /**
     * Convert a Tailwind-style accent into a CSS gradient value.
     */
    public static function css(?string $accent, string $fallback = 'linear-gradient(135deg, #0A2240, #1A3A5C)'): string
    {
        if ($accent && preg_match_all('/#([0-9a-fA-F]{3,8})/', $accent, $matches) && count($matches[1]) >= 2) {
            return 'linear-gradient(135deg, #'.$matches[1][0].', #'.$matches[1][1].')';
        }

        return $fallback;
    }
}
