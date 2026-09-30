@extends('layouts.site')

@section('title', $post['title'].' — Pakka Patriot')
@section('description', $post['excerpt'])

@section('content')
    <div class="min-h-screen bg-brand-cream relative">

        {{-- Back bar --}}
        <div class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-[#F0EBE0]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
                <a href="{{ route('shop.blog.index') }}"
                   class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                    <x-pp-icon name="arrow-left" :size="18" />
                    Back
                </a>
                <span class="text-[10px] font-black tracking-widest text-[#8A9EB4] uppercase">
                    PakkaPatriot Story
                </span>
            </div>
        </div>

        {{-- Cover image --}}
        <div class="relative h-64 sm:h-80 md:h-96 w-full overflow-hidden bg-gray-100">
            <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}"
                 class="w-full h-full object-cover" referrerpolicy="no-referrer">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>

            <div class="absolute bottom-6 left-4 sm:left-8">
                <x-pp-badge :category="$post['category']" class="text-xs px-4 py-1.5 shadow-lg" />
            </div>
        </div>

        {{-- Story content --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-8 lg:px-12 py-8 sm:py-12">
            <article class="space-y-8">

                {{-- Title and meta --}}
                <div class="space-y-4 text-left">
                    <h1 class="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-[#0A2240] tracking-tight leading-tight">
                        {{ $post['title'] }}
                    </h1>

                    <div class="flex flex-wrap gap-4 text-xs font-bold text-[#8A9EB4] uppercase tracking-wider font-sans">
                        <div class="flex items-center gap-1.5">
                            <x-pp-icon name="user" :size="14" />
                            <span>{{ $post['author_name'] ?: 'Pakka Patriot' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <x-pp-icon name="calendar" :size="14" />
                            <span>{{ $post['date'] }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <x-pp-icon name="clock" :size="14" />
                            <span>{{ $post['read_time'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Excerpt highlight --}}
                @if($post['excerpt'])
                    <div class="bg-[#FAF6EC] border-l-4 border-[#F6B828] rounded-r-xl p-5">
                        <p class="text-sm sm:text-md font-semibold text-[#2F445A] leading-relaxed italic">
                            {{ $post['excerpt'] }}
                        </p>
                    </div>
                @endif

                {{-- Article body --}}
                <div class="text-sm sm:text-md text-[#2F445A] leading-relaxed font-sans text-left">
                    @if($post['content'])
                        <div class="pp-article">{!! $post['content'] !!}</div>
                    @else
                        <div class="space-y-5">
                            <p>
                                This article is a deep-dive story exploring Bhārat's rich history, traditions, and culture.
                                Read the full post on pakkapatriot.com using the link below.
                            </p>
                            <div class="bg-[#FAF6EC] rounded-2xl p-6 border border-[#E4DCB9]">
                                <p class="text-sm font-semibold text-[#0A2240] mb-3">
                                    📖 Full story available at PakkaPatriot.com
                                </p>
                                <a href="{{ $post['link'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 bg-[#F6B828] hover:bg-[#DAA520] text-white px-5 py-2.5 rounded-full text-xs font-bold transition-all">
                                    Read original post
                                    <x-pp-icon name="arrow-up-right" :size="14" />
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Bottom navigation --}}
                <div class="pt-8 border-t border-[#F0EBE0] flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('shop.blog.index') }}"
                       class="flex items-center gap-2 text-sm font-bold text-[#0A2240] hover:text-[#F6B828] transition-colors">
                        <x-pp-icon name="arrow-left" :size="16" />
                        Back to stories
                    </a>

                    <a href="{{ $post['link'] }}" target="_blank" rel="noopener noreferrer"
                       class="text-xs font-semibold text-[#8A9EB4] hover:text-[#F6B828] transition-colors flex items-center gap-1">
                        <x-pp-icon name="arrow-up-right" :size="12" />
                        View on PakkaPatriot.com
                    </a>
                </div>
            </article>
        </div>
    </div>
@endsection
