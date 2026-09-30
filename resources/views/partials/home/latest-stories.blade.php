@php
    $posts = $posts ?? [];
@endphp

{{-- Content only: the surrounding section/grid lives in home.blade.php. --}}
<div id="latest-stories" class="scroll-mt-32 h-full">

    <div class="flex justify-between items-end mb-8 select-none">
        <div class="text-left">
            <h2 class="font-brush text-4xl sm:text-5xl text-[#0A2240] tracking-wide">
                LATEST <span class="text-[#F6B828]">STORIES</span>
            </h2>
        </div>

        <a href="{{ route('shop.blog.index') }}"
           class="flex items-center gap-2 text-sm sm:text-md font-bold text-[#F6B828] hover:text-[#DAA520] transition-colors group">
            View all stories
            <x-pp-icon name="arrow-right" :size="16" class="transition-transform group-hover:translate-x-1" />
        </a>
    </div>

    @if(count($posts) === 0)
        <div class="bg-[#FAF6EC] border border-[#E4DCB9] rounded-2xl p-12 text-center max-w-md mx-auto">
            <p class="font-display font-bold text-lg text-[#0A2240] mb-2">No Stories Found</p>
            <p class="text-sm text-[#2F445A] mb-4">We couldn't find stories matching this filter. Tap below to reset.</p>
            <a href="{{ route('shop.blog.index') }}"
               class="inline-block bg-[#F6B828] hover:bg-[#DAA520] text-white px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow">
                Show All Stories
            </a>
        </div>
    @else
        <div class="relative group/carousel" data-stories-carousel>
            <button type="button" data-stories-prev title="Scroll Left"
                    class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-[#E4DCB9] text-[#0A2240] hover:text-[#F6B828] p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hidden sm:flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 focus:opacity-100">
                <x-pp-icon name="chevron-left" :size="20" />
            </button>
            <button type="button" data-stories-next title="Scroll Right"
                    class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-[#F6B828] hover:bg-[#DAA520] text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hidden sm:flex items-center justify-center opacity-0 group-hover/carousel:opacity-100 focus:opacity-100">
                <x-pp-icon name="chevron-right" :size="20" />
            </button>

            <div data-stories-track class="flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-none pb-4 px-1">
                @foreach($posts as $post)
                    <div class="flex-shrink-0 w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%_-_16px)] snap-start">
                        <a href="{{ url('/'.$post['slug']) }}"
                           class="h-full bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300 flex flex-col cursor-pointer group">
                            <div class="relative h-48 sm:h-52 overflow-hidden bg-[#FAF6EC]">
                                <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy" referrerpolicy="no-referrer">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
                            </div>

                            <div class="p-6 flex-grow flex flex-col items-start text-left">
                                <x-pp-badge :category="$post['category']" class="mb-3" />

                                <h3 class="font-display font-extrabold text-md sm:text-lg text-[#0A2240] tracking-tight leading-snug mb-2 flex-grow group-hover:text-[#F6B828] transition-colors">
                                    {{ $post['title'] }}
                                </h3>

                                <div class="w-full border-t border-[#F0EBE0]/80 pt-4 mt-4 flex justify-between items-center text-[11px] font-bold text-[#8A9EB4] uppercase tracking-wide font-sans">
                                    <span>{{ $post['author_name'] ?: 'PATRIOT' }}</span>
                                    <span>{{ $post['read_time'] ?? '3 MIN' }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
