<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Support\GameServer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * PLAY — the games index and the five playable boards.
 *
 * Ported from the React PlayPage and the per-game pages. Each game is a
 * self-contained HTML app in public/ that the page frames; the online-room
 * server address is resolved here (see App\Support\GameServer).
 */
class PlayController extends Controller
{
    /**
     * Display order of the boards, matching the React games list.
     */
    protected const ORDER = [
        '/play/pachisi',
        '/play/chaukabaara',
        '/play/aadu-puli-aatam',
        '/play/chaturvimshati',
        '/play/vish-amrit',
    ];

    /**
     * Online-room games: the slug, and the folder holding the game's HTML app.
     * Pachisi is hot-seat only and has no server.
     */
    protected const ONLINE_GAMES = [
        'chaukabaara' => 'chaukabaara',
        'aadu-puli-aatam' => 'aadupuliatam',
        'chaturvimshati' => 'chaturvimshati',
        'vish-amrit' => 'vishamrit',
    ];

    public function index(): View
    {
        return view('play.index', ['games' => $this->games()]);
    }

    public function show(Request $request, string $game): View
    {
        $folder = self::ONLINE_GAMES[$game] ?? ($game === 'pachisi' ? 'pachisi' : null);

        abort_if($folder === null, 404);

        $server = GameServer::resolve($game, $request->query('server'));

        // ?room=XXXX lets a shared join link open straight into a lobby.
        $room = $request->query('room');
        $room = is_string($room) && $room !== '' ? mb_strtoupper($room) : null;

        return view('play.game', [
            'game' => $game,
            'folder' => $folder,
            'online' => isset(self::ONLINE_GAMES[$game]),
            'server' => $server,
            'room' => $room,
            'gameSrc' => $this->gameSrc($folder, $server, $room, isset(self::ONLINE_GAMES[$game])),
        ]);
    }

    /**
     * The iframe URL for a board, carrying the server (and room) into the game.
     */
    protected function gameSrc(string $folder, string $server, ?string $room, bool $online): string
    {
        if (! $online) {
            return url('/'.$folder.'/index.html');
        }

        $query = ['server' => $server];

        if ($room !== null) {
            $query['room'] = $room;
        }

        return url('/'.$folder.'/index.html').'?'.http_build_query($query);
    }

    /**
     * Every board, in display order.
     *
     * @return array<int, Game>
     */
    protected function games(): array
    {
        $games = Game::all()->all();

        usort($games, fn (Game $a, Game $b) => array_search($a->path, self::ORDER, true) <=> array_search($b->path, self::ORDER, true));

        return $games;
    }

    /**
     * Tag chips for a board, as [icon, label] pairs.
     *
     * @return array<int, array{icon: string, label: string}>
     */
    public static function tags(Game $game): array
    {
        $tags = is_array($game->tags) ? $game->tags : (json_decode((string) $game->tags, true) ?: []);

        return array_values(array_map(fn ($tag) => [
            'icon' => (string) ($tag['icon'] ?? 'sparkles'),
            'label' => (string) ($tag['label'] ?? ''),
        ], array_filter($tags, 'is_array')));
    }
}
