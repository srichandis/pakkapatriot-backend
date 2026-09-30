@extends('layouts.site')

@section('title', $meta['title'].' — Free Downloads — Pakka Patriot')
@section('description', $meta['blurb'])

@php
    $available = collect($items)->where('available', true)->count();
@endphp

{{--
    One material type. An item whose file has not been uploaded yet is shown
    as "Coming soon" rather than as a link — see App\Http\Controllers\
    DownloadController::items() for the expected path.
--}}
@section('content')
<div class="bg-brand-cream min-h-screen">

    {{-- HERO --}}
    <section class="relative bg-[#0A2240] text-white overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none select-none" aria-hidden="true">
            <div class="absolute top-8 right-10 w-44 h-44 border-t-4 border-l-4 border-white rounded-tl-full"></div>
            <div class="absolute bottom-6 left-8 w-40 h-40 border-b-4 border-r-4 border-white rounded-br-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 text-left">
            <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#B5CADF] mb-4" aria-label="Breadcrumb">
                <a href="{{ route('downloads') }}" class="hover:text-[#F6B828] transition-colors">Downloads</a>
                <x-pp-icon name="chevron-right" :size="12" />
                <span class="text-[#F6B828]">{{ $meta['title'] }}</span>
            </nav>

            <div class="flex items-start gap-4">
                <span class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-white/10 border border-white/15 text-3xl">
                    {{ $meta['emoji'] }}
                </span>
                <div class="min-w-0">
                    <h1 class="font-brush text-4xl sm:text-5xl leading-tight tracking-wide">
                        {{ $meta['title'] }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm sm:text-base text-[#B5CADF] font-medium leading-relaxed">
                        {{ $meta['blurb'] }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <span class="bg-white/10 border border-white/15 rounded-full px-4 py-2 text-xs font-bold text-white">
                    {{ count($items) }} {{ \Illuminate\Support\Str::plural('download', count($items)) }}
                </span>
                @if($available < count($items))
                    <span class="bg-[#F6B828]/15 border border-[#F6B828]/30 rounded-full px-4 py-2 text-xs font-bold text-[#F6B828]">
                        {{ $available }} ready now
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- ITEMS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="space-y-4">
            @foreach($items as $item)
                <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-sm hover:shadow-md transition-shadow p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-5">

                    <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border text-xl"
                          style="background-color: {{ $meta['accent'] }}14; border-color: {{ $meta['accent'] }}33">
                        {{ $meta['emoji'] }}
                    </span>

                    <div class="min-w-0 flex-grow text-left">
                        <h2 class="font-display font-black text-base sm:text-lg text-[#0A2240] tracking-tight">
                            {{ $item['title'] }}
                        </h2>
                        <p class="mt-1 text-sm text-[#4E637A] font-medium leading-relaxed">
                            {{ $item['description'] }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#0A2240] bg-[#FAF6EC] border border-[#E4DCB9] px-2.5 py-1 rounded-full">
                                {{ strtoupper($item['format']) }}
                            </span>
                            <span class="text-[11px] font-bold text-[#8A9EB4]">{{ $item['meta'] }}</span>
                        </div>
                    </div>

                    @if($item['available'])
                        <a href="{{ $item['url'] }}" download
                           class="flex-shrink-0 inline-flex items-center justify-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-6 py-3 rounded-full text-sm font-black transition-colors">
                            <x-pp-icon name="download" :size="16" />
                            Download
                        </a>
                    @else
                        <span class="flex-shrink-0 inline-flex items-center justify-center gap-2 bg-[#FAF6EC] border border-[#E4DCB9] text-[#8A9EB4] px-6 py-3 rounded-full text-sm font-black select-none">
                            <x-pp-icon name="clock" :size="16" />
                            Coming soon
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- OTHER MATERIALS --}}
        <div class="mt-14">
            <h2 class="font-display font-black text-sm uppercase tracking-widest text-[#8A9EB4] mb-4">
                More to download
            </h2>
            <div class="flex flex-wrap gap-3">
                @foreach($siblings as $sibling)
                    <a href="{{ route('downloads.category', $sibling['slug']) }}"
                       class="inline-flex items-center gap-2 bg-white border border-[#E4DCB9] hover:border-[#F6B828] hover:text-[#F6B828] text-[#0A2240] px-4 py-2.5 rounded-full text-xs font-bold transition-colors">
                        <span>{{ $sibling['emoji'] }}</span>
                        {{ $sibling['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
