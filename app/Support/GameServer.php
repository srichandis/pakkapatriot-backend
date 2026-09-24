<?php

namespace App\Support;

/**
 * WebSocket server resolution for the online board games.
 *
 * Ported from the React game pages: a `?server=` override is only honoured when
 * it targets the same host as the configured default, so a crafted share link
 * (?server=ws://evil.example) can't point players at a rogue server.
 */
class GameServer
{
    /**
     * The configured server for a game, falling back to the shared default.
     */
    public static function configured(string $game): string
    {
        $configured = config('games.servers')[$game] ?? null;

        return self::toWebSocketUrl($configured ?: (string) config('games.default_server'));
    }

    /**
     * The server to actually use: the override when it is same-host, else the default.
     */
    public static function resolve(string $game, ?string $override): string
    {
        $fallback = self::configured($game);

        if (! $override) {
            return $fallback;
        }

        $candidate = self::toWebSocketUrl($override);

        if (self::host($candidate) === self::host($fallback)) {
            return $candidate;
        }

        return $fallback;
    }

    /**
     * Normalise an http(s)/ws(s)/bare-host value into a ws(s) URL.
     */
    public static function toWebSocketUrl(string $server): string
    {
        $server = trim($server);

        if (preg_match('#^wss?://#i', $server)) {
            return $server;
        }

        if (preg_match('#^https://#i', $server)) {
            return 'wss://'.substr($server, 8);
        }

        if (preg_match('#^http://#i', $server)) {
            return 'ws://'.substr($server, 7);
        }

        // A bare host — let the game resolve the protocol.
        return $server;
    }

    /**
     * Host (and port) of a server URL, or null when it can't be parsed.
     */
    protected static function host(string $url): ?string
    {
        $parts = parse_url($url);

        if (! isset($parts['host'])) {
            return null;
        }

        return $parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
    }
}
