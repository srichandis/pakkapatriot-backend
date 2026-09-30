@php
    $categories = [
        ['id' => 'PEOPLE', 'label' => 'PEOPLE', 'icon' => 'users', 'iconColor' => 'text-rose-500', 'bg' => 'bg-rose-50 border-rose-200', 'href' => route('collection.browse', 'people')],
        ['id' => 'IDEAS', 'label' => 'IDEAS', 'icon' => 'lightbulb', 'iconColor' => 'text-amber-500', 'bg' => 'bg-amber-50 border-amber-200', 'href' => route('collection.browse', 'ideas')],
        ['id' => 'PLACES', 'label' => 'PLACES', 'icon' => 'map-pin', 'iconColor' => 'text-emerald-500', 'bg' => 'bg-emerald-50 border-emerald-200', 'href' => route('collection.browse', 'places')],
        ['id' => 'CULTURE', 'label' => 'CULTURE', 'icon' => 'palette', 'iconColor' => 'text-violet-500', 'bg' => 'bg-violet-50 border-violet-200', 'href' => route('collection.browse', 'culture')],
        ['id' => 'CREATE', 'label' => 'CREATE', 'icon' => 'sparkles', 'iconColor' => 'text-sky-500', 'bg' => 'bg-sky-50 border-sky-200', 'href' => url('/create')],
        ['id' => 'PLAY', 'label' => 'PLAY', 'icon' => 'gamepad-2', 'iconColor' => 'text-[#F6B828]', 'bg' => 'bg-yellow-50 border-yellow-200', 'href' => url('/play')],
        ['id' => 'MADE_IN_BHARAT', 'label' => 'MADE IN BHĀRAT', 'icon' => 'badge-check', 'iconColor' => 'text-orange-500', 'bg' => 'bg-orange-50 border-orange-200', 'href' => url('/shop')],
    ];
@endphp

<section id="what-pakka-loves" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center scroll-mt-32">

    <div class="flex items-center justify-center gap-4 mb-10 select-none">
        <svg class="w-8 h-8 text-[#F6B828]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M3 12 L7 10 L7 14 Z M17 10 L21 12 L17 14 Z" />
        </svg>
        <h2 class="font-brush text-4xl sm:text-5xl text-[#0A2240] tracking-wide">
            WHAT <span class="text-[#F6B828]">PAKKA PATRIOT LOVES</span>
        </h2>
        <svg class="w-8 h-8 text-[#F6B828]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M3 12 L7 10 L7 14 Z M17 10 L21 12 L17 14 Z" />
        </svg>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
        @foreach($categories as $cat)
            <a href="{{ $cat['href'] }}"
               class="flex flex-col items-center p-6 bg-white rounded-2xl border-2 cursor-pointer transition-all duration-300 transform select-none border-[#F0EBE0] hover:border-[#F6B828] hover:shadow-lg hover:-translate-y-1">
                <div class="p-4 rounded-xl mb-4 {{ $cat['bg'] }} flex items-center justify-center transition-colors">
                    <x-pp-icon :name="$cat['icon']" :size="32" class="{{ $cat['iconColor'] }}" />
                </div>

                <span class="font-display font-bold text-sm text-[#0A2240] tracking-tight text-center">
                    {{ $cat['label'] }}
                </span>
            </a>
        @endforeach
    </div>
</section>
