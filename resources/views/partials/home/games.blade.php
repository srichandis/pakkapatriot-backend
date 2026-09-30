@php
    /*
     * "Games from Bhārat" — the six-across showcase of the playable boards,
     * sitting directly beneath the Latest Stories row.
     *
     * The five boards and their running order come from PlayController::games(),
     * so this strip stays in step with /play.
     *
     * Each tile leads with a raster board illustration from public/images/games/
     * — the five boards drawn in the hero banner's navy (#0A2240) and gold
     * (#F6B828). Titles are long, so the subtitle is the short half of the
     * tagline: "Poison & Nectar" rather than "Poison & Nectar — the ancient
     * freeze-tag chase".
     *
     * A board with no illustration of its own (a new game row, say) falls back
     * to its first tag icon on a tinted badge, so the grid never renders a
     * broken tile.
     */
    $games = $games ?? [];

    $look = [
        '/play/pachisi' => ['image' => 'images/games/pachisi.png', 'icon' => 'dices', 'color' => '#C2410C'],
        '/play/chaukabaara' => ['image' => 'images/games/chaukabaara.png', 'icon' => 'shell', 'color' => '#2563EB'],
        '/play/aadu-puli-aatam' => ['image' => 'images/games/aadu-puli-aatam.png', 'icon' => 'paw-print', 'color' => '#15803D'],
        '/play/chaturvimshati' => ['image' => 'images/games/chaturvimshati.png', 'icon' => 'layout-grid', 'color' => '#7C3AED'],
        '/play/vish-amrit' => ['image' => 'images/games/vish-amrit.png', 'icon' => 'flask-conical', 'color' => '#BE123C'],
    ];
@endphp

@if(count($games))
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 border-t border-[#F0EBE0]/60">

        <div class="flex justify-between items-end mb-8 select-none">
            <div class="text-left">
                <h2 class="font-brush text-4xl sm:text-5xl text-[#0A2240] tracking-wide">
                    GAMES FROM <span class="text-[#F6B828]">BHĀRAT</span>
                </h2>
            </div>

            <a href="{{ route('play') }}"
               class="flex items-center gap-2 text-sm sm:text-md font-bold text-[#F6B828] hover:text-[#DAA520] transition-colors group">
                More
                <x-pp-icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
            </a>
        </div>

        {{-- Five across — one column per board — scrolls sideways on narrow screens. --}}
        <div class="overflow-x-auto pb-2">
            <div class="grid grid-cols-5 gap-3 sm:gap-4 min-w-[680px]">
                @foreach($games as $game)
                    @php
                        $tagIcon = \App\Http\Controllers\PlayController::tags($game)[0]['icon'] ?? 'sparkles';
                        // A cover image uploaded from the admin wins over the
                        // bundled illustration for that board.
                        $image = $game->image_url
                            ?: (isset($look[$game->path]['image']) ? asset($look[$game->path]['image']) : null);
                        $icon = $look[$game->path]['icon'] ?? \Illuminate\Support\Str::kebab($tagIcon);
                        $color = $look[$game->path]['color'] ?? '#0A2240';
                        $subtitle = trim(explode('—', (string) $game->tagline)[0]) ?: (string) $game->badge;
                    @endphp

                    <a href="{{ url($game->path) }}"
                       class="group flex flex-col overflow-hidden rounded-2xl bg-white border border-[#F0EBE0] cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        @if($image)
                            <div class="relative aspect-[4/3] overflow-hidden bg-[#0A2240]">
                                <img src="{{ $image }}" alt="{{ $game->title }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                     loading="lazy" referrerpolicy="no-referrer">
                            </div>
                        @else
                            <div class="flex items-center justify-center aspect-[4/3] bg-[#FAF6EC]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-2xl border"
                                      style="background-color: {{ $color }}1A; border-color: {{ $color }}33; color: {{ $color }}">
                                    <x-pp-icon :name="$icon" :size="26" />
                                </span>
                            </div>
                        @endif

                        <div class="flex flex-col items-center text-center p-4">
                            <h3 class="font-display font-black text-sm text-[#0A2240] tracking-tight leading-snug">
                                {{ $game->title }}
                            </h3>

                            <p class="text-[11px] text-[#2F445A]/70 font-medium leading-snug mt-1 line-clamp-2">
                                {{ $subtitle }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
