@extends('layouts.site')

@section('title', 'News — Achievements of Bhārat — Pakka Patriot')
@section('description', "What Bhārat is achieving right now: space, sport, innovation, defence and culture, collected from the country's newspapers and refreshed every morning.")

{{--
    NEWS — headlines about the country's achievements, merged from the RSS
    topics in config/news.php and refreshed daily by `news:refresh`.
    Headlines link out to the publisher; nothing is republished here.
--}}
@section('content')
<div class="bg-brand-cream min-h-screen">

    {{-- HERO --}}
    <section class="relative bg-[#0A2240] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none" aria-hidden="true">
            <div class="absolute top-8 left-10 w-44 h-44 border-t-4 border-r-4 border-white rounded-tr-full"></div>
            <div class="absolute bottom-6 right-8 w-40 h-40 border-b-4 border-l-4 border-white rounded-bl-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 text-left">
            <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-black tracking-[0.25em] uppercase text-[#F6B828] mb-3">
                <x-pp-icon name="newspaper" :size="14" /> In the News
            </span>
            <h1 class="font-brush text-4xl sm:text-6xl lg:text-7xl leading-tight tracking-wide">
                Bhārat, <span class="text-[#F6B828]">Achieving</span>
            </h1>
            <p class="mt-4 max-w-2xl text-sm sm:text-base text-[#B5CADF] font-medium leading-relaxed">
                Rockets, records, research and recognition — a running collection of what the country is
                getting done, gathered from its newspapers and refilled every morning.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <span class="bg-white/10 border border-white/15 rounded-full px-4 py-2 text-xs font-bold text-white">
                    {{ $total }} stories
                </span>
                @if($refreshedAt)
                    <span class="bg-white/10 border border-white/15 rounded-full px-4 py-2 text-xs font-bold text-[#F6B828]">
                        Updated {{ $refreshedAt->diffForHumans() }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- TOPIC FILTERS --}}
    @if(count($topics))
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div class="flex flex-wrap gap-3 select-none">
                <a href="{{ route('news') }}"
                   class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $topic === '' ? 'bg-[#0A2240] text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-[#0A2240] hover:text-[#0A2240]' }}">
                    ALL NEWS
                </a>
                @foreach($topics as $name)
                    <a href="{{ route('news', ['topic' => $name]) }}"
                       class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all duration-200 {{ $topic === $name ? 'bg-[#0A2240] text-white shadow-md' : 'bg-white border border-[#E4DCB9] text-[#4E637A] hover:border-[#0A2240] hover:text-[#0A2240]' }}">
                        {{ $name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- HEADLINES --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

        @if(empty($items))
            <div class="text-center py-16 bg-white rounded-3xl border border-[#F0EBE0] px-6">
                <x-pp-icon name="newspaper" :size="56" class="mx-auto text-[#E4DCB9] mb-4" />
                <h2 class="font-display font-bold text-xl text-[#0A2240] mb-2">No headlines right now</h2>
                <p class="text-sm text-[#4E637A] font-medium max-w-md mx-auto">
                    We couldn't reach the news feeds. Try again in a little while, or
                    <a href="{{ route('news') }}" class="text-[#F6B828] font-bold hover:text-[#DAA520]">reload the page</a>.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($items as $item)
                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer nofollow"
                       class="group bg-white rounded-2xl border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-lg transition-all duration-300 p-5 sm:p-6 flex flex-col text-left">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#0A2240] bg-[#FAF6EC] border border-[#E4DCB9] px-2.5 py-1 rounded-full">
                                {{ $item['topic'] }}
                            </span>
                            <span class="text-[11px] font-bold text-[#8A9EB4] truncate">{{ $item['source'] }}</span>
                        </div>

                        <h2 class="font-display font-extrabold text-base sm:text-lg text-[#0A2240] tracking-tight leading-snug group-hover:text-[#F6B828] transition-colors">
                            {{ $item['title'] }}
                        </h2>

                        <div class="mt-auto pt-4 flex items-center justify-between text-[11px] font-bold uppercase tracking-wide text-[#8A9EB4]">
                            <span>{{ $item['published_at']->diffForHumans() }}</span>
                            <span class="inline-flex items-center gap-1 text-[#F6B828]">
                                Read
                                <x-pp-icon name="arrow-up-right" :size="13" class="transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <p class="mt-10 text-center text-xs font-semibold text-[#8A9EB4] max-w-2xl mx-auto">
                Headlines are collected automatically from Indian news publishers and link straight to their
                reports. The list refills every morning.
            </p>
        @endif
    </section>

</div>
@endsection
