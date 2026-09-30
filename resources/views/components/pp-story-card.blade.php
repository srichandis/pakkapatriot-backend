@props(['post'])

<a href="{{ route('shop.blog.show', $post['slug']) }}"
   class="group h-full bg-white rounded-3xl overflow-hidden border border-[#F0EBE0] hover:border-[#F6B828]/40 shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300 flex flex-col cursor-pointer">
    <div class="relative h-48 sm:h-52 overflow-hidden bg-[#FAF6EC]">
        <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}"
             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
             loading="lazy" referrerpolicy="no-referrer">
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
    </div>

    <div class="p-6 flex-grow flex flex-col items-start text-left">
        <x-pp-badge :category="$post['category']" class="mb-3" />

        <h3 class="font-display font-extrabold text-md sm:text-lg text-[#0A2240] tracking-tight leading-snug mb-2 group-hover:text-[#F6B828] transition-colors line-clamp-2">
            {{ $post['title'] }}
        </h3>

        <p class="font-sans text-xs sm:text-sm text-[#4E637A] font-medium leading-relaxed flex-grow line-clamp-3">
            {{ $post['excerpt'] }}
        </p>

        <div class="w-full border-t border-[#F0EBE0]/80 pt-4 mt-4 flex justify-between items-center text-[11px] font-bold text-[#8A9EB4] uppercase tracking-wide font-sans">
            <span>{{ $post['author_name'] ?: 'PATRIOT' }}</span>
            <span class="flex items-center gap-1">
                {{ $post['read_time'] }}
                <x-pp-icon name="arrow-right" :size="12" class="text-[#F6B828] group-hover:translate-x-0.5 transition-transform" />
            </span>
        </div>
    </div>
</a>
