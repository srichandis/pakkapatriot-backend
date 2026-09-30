@extends('layouts.site')

@php
    $words = explode(' ', $doc['title']);
    $titleLead = array_shift($words);
    $titleRest = implode(' ', $words);
    $icon = $doc['slug'] === 'privacy' ? 'shield-check' : 'file-text';
@endphp

@section('title', $doc['title'].' — Pakka Patriot')
@section('description', $doc['intro'])

@section('content')
    <div class="min-h-screen bg-brand-cream relative overflow-hidden">
        {{-- Background decor --}}
        <div class="absolute top-20 right-0 w-80 h-80 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 left-0 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Sticky top bar --}}
        <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
                <a href="{{ url('/') }}"
                   class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                    <x-pp-icon name="arrow-left" :size="18" />
                    Back
                </a>
                <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                    Pakka Patriot · Legal
                </span>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 relative z-10">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-[#0A2240]/5 rounded-full px-4 py-1.5 mb-4">
                    <x-pp-icon :name="$icon" :size="16" class="text-[#F6B828]" />
                    <span class="text-xs font-black tracking-widest text-[#0A2240] uppercase">Pakka Patriot</span>
                </div>
                <h1 class="font-brush text-4xl sm:text-5xl text-[#0A2240] tracking-wide leading-tight">
                    {{ $titleLead }} <span class="text-[#F6B828]">{{ $titleRest }}</span>
                </h1>
                <p class="text-xs font-bold text-[#8A9EB4] uppercase tracking-wider mt-3">{{ $doc['updated'] }}</p>
                <p class="text-[#4E637A] font-medium text-sm sm:text-base max-w-2xl mx-auto mt-4 leading-relaxed">
                    {{ $doc['intro'] }}
                </p>
            </div>

            {{-- Sections --}}
            <div class="space-y-6">
                @foreach($doc['sections'] as $section)
                    <div class="bg-white rounded-3xl border border-[#F0EBE0] p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="w-8 h-8 rounded-full bg-[#FAF6EC] border border-[#E4DCB9] text-[#F6B828] font-black flex items-center justify-center text-sm">
                                {{ $loop->iteration }}
                            </span>
                            <h2 class="font-display font-black text-lg sm:text-xl text-[#0A2240] tracking-tight">
                                {{ $section['heading'] }}
                            </h2>
                        </div>
                        <div class="space-y-2.5 pl-11">
                            @foreach($section['body'] as $paragraph)
                                <p class="text-sm text-[#4E637A] font-medium leading-relaxed">{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer note --}}
            <div class="mt-10 text-center">
                <p class="text-xs text-[#8A9EB4] font-semibold">
                    Questions? Write to us at
                    <a href="mailto:support@pakkapatriot.com"
                       class="text-[#F6B828] hover:text-[#DAA520] font-bold underline transition-colors">
                        support@pakkapatriot.com
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
