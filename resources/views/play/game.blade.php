@extends('layouts.game')

@php
    /**
     * One full-screen board — ported from the React game pages.
     *
     * The values below are presentation (colours, icon, copy), so they live in
     * the view where Tailwind's scanner can see the class names.
     */
    $boards = [
        'chaukabaara' => [
            'name' => 'Chaukabaara',
            'icon' => 'shell',
            'iconClass' => 'text-[#F6B828]',
            'barBg' => 'bg-[#0A2240]',
            'barBorder' => 'border-[#1F3D5E]',
            'bannerBg' => 'bg-[#0A2240]/95',
            'bannerBorder' => 'border-[#1F3D5E]',
            'iframeTitle' => 'Chaukabaara — ancient board game of Bhārat',
            'envVar' => 'VITE_CHAUK_SERVER',
            'hint' => 'Start it with "node server.js" or set VITE_CHAUK_SERVER.',
        ],
        'aadu-puli-aatam' => [
            'name' => 'Aadu Puli Aatam',
            'icon' => 'swords',
            'iconClass' => 'text-[#F6B828]',
            'barBg' => 'bg-[#0C2419]',
            'barBorder' => 'border-[#1F3D5E]',
            'bannerBg' => 'bg-[#0C2419]/95',
            'bannerBorder' => 'border-[#1F3D5E]',
            'iframeTitle' => 'Aadu Puli Aatam — Goats & Tigers, ancient Tamil Nadu board game',
            'envVar' => 'VITE_AP_SERVER',
            'hint' => 'Play hot-seat or vs the computer now, or start the server with "npm run server" and set VITE_AP_SERVER.',
        ],
        'chaturvimshati' => [
            'name' => 'Chaturvimshati Koṣṭaka',
            'icon' => 'layout-grid',
            'iconClass' => 'text-[#F6B828]',
            'barBg' => 'bg-[#1d1026]',
            'barBorder' => 'border-[#3a1f4a]',
            'bannerBg' => 'bg-[#1d1026]/95',
            'bannerBorder' => 'border-[#3a1f4a]',
            'iframeTitle' => 'Chaturvimshati Koṣṭaka — Twenty-Four Squares, ancient board game of Bhārat',
            'envVar' => 'VITE_CKV_SERVER',
            'hint' => 'Play hot-seat or vs the computer now, or start the server with "npm run server" and set VITE_CKV_SERVER.',
        ],
        'vish-amrit' => [
            'name' => 'Vish & Amrit',
            'icon' => 'skull',
            'iconClass' => 'text-[#a33a5e]',
            'barBg' => 'bg-[#0F1912]',
            'barBorder' => 'border-[#2A4A32]',
            'bannerBg' => 'bg-[#0F1912]/95',
            'bannerBorder' => 'border-[#2A4A32]',
            'iframeTitle' => 'Vish & Amrit — the poison chase, an ancient freeze-tag board game of Bhārat',
            'envVar' => 'VITE_VAM_SERVER',
            'hint' => 'Play hot-seat or vs the computer now, or start the server with "npm run server" and set VITE_VAM_SERVER.',
        ],
        'pachisi' => [
            'name' => 'Pachisi',
            'icon' => 'dices',
            'iconClass' => 'text-[#F6B828]',
            'barBg' => 'bg-[#2a1220]',
            'barBorder' => 'border-[#4a3520]',
            'linkClass' => 'text-[#c3ad92]',
            'iframeTitle' => 'Pachisi — Twenty-Five, the royal cross-board game of ancient Bhārat',
            'badge' => 'Hot-seat · 2–4 players',
            'badgeClass' => 'bg-[#3a1f2c] text-[#f2d68a]',
        ],
    ];

    $board = $boards[$game];
    $linkClass = $board['linkClass'] ?? 'text-[#B5CADF]';
@endphp

@section('title', $board['name'].' — Play — Pakka Patriot')

@section('content')
<div class="h-dvh w-full flex flex-col {{ $board['barBg'] }}">

    {{-- Slim control bar --}}
    <div class="flex items-center justify-between gap-3 px-3 sm:px-5 py-2.5 {{ $board['barBg'] }} border-b {{ $board['barBorder'] }} text-white shrink-0">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ url('/play') }}"
               class="flex items-center gap-1.5 text-xs font-bold {{ $linkClass }} hover:text-[#F6B828] transition-colors shrink-0">
                <x-pp-icon name="arrow-left" :size="16" /> Back to Play
            </a>
            <span class="hidden sm:flex items-center gap-1.5 text-sm font-black tracking-wide min-w-0">
                <x-pp-icon :name="$board['icon']" :size="15" :class="$board['iconClass'].' shrink-0'" />
                <span class="truncate">{{ $board['name'] }}</span>
            </span>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($online)
                {{-- Filled in by resources/js/app.js after probing the game server --}}
                <span id="gameServerStatus" class="hidden md:flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-[#4D1B1B] text-[#FF9B9B]"
                      title="Checking the game server…">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF9B9B]"></span>
                    <span id="gameServerLabel">Checking…</span>
                </span>

                <button type="button" id="gameCopyLink"
                        class="flex items-center gap-1.5 text-[11px] font-bold {{ $linkClass }} hover:text-white border {{ $board['barBorder'] }} hover:border-[#F6B828] px-2.5 py-1.5 rounded-full transition-colors"
                        title="Copy a join link for this game">
                    <x-pp-icon name="copy" id="gameCopyIcon" :size="13" />
                    <span class="hidden sm:inline" id="gameCopyLabel">Copy link</span>
                </button>
            @elseif(! empty($board['badge']))
                <span class="hidden md:flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full {{ $board['badgeClass'] }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#F6B828] animate-pulse"></span>
                    {{ $board['badge'] }}
                </span>
            @endif
        </div>
    </div>

    {{-- The game, full-screen --}}
    <div class="flex-1 min-h-0 relative bg-[#FCFAF5]"
         @if($online) data-game-shell data-game-server="{{ $server }}" @endif>
        <iframe
            src="{{ $gameSrc }}"
            title="{{ $board['iframeTitle'] }}"
            class="absolute inset-0 w-full h-full border-0"
            allow="clipboard-write; autoplay"
        ></iframe>

        @if($online)
            <div id="gameServerBanner" class="hidden absolute bottom-3 left-1/2 -translate-x-1/2 z-10 max-w-md w-[calc(100%-24px)] {{ $board['bannerBg'] }} backdrop-blur text-white text-[11px] font-medium px-4 py-2.5 rounded-xl border {{ $board['bannerBorder'] }} shadow-2xl items-start gap-2">
                <x-pp-icon name="info" :size="14" class="text-[#F6B828] shrink-0 mt-0.5" />
                <span class="flex-grow">
                    <b class="font-black">Online rooms are unavailable</b> — the game server isn't reachable at
                    <code class="text-[#F6B828]">{{ $server }}</code>. {{ $board['hint'] }}
                </span>
                <button type="button" id="gameServerBannerClose"
                        class="shrink-0 text-[#B5CADF] hover:text-white transition-colors" title="Dismiss">
                    <x-pp-icon name="x" :size="14" />
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
