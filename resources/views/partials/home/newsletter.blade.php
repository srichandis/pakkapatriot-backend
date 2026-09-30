<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
    {{-- Warm cream strip from design/subscribe.jpg, copy left and form right. --}}
    <div class="bg-[#FDF2E0] rounded-3xl p-8 sm:p-12 border border-[#F7E6C8] relative overflow-hidden shadow-sm flex flex-col lg:flex-row items-center gap-8 lg:gap-16">

        {{-- Decorative paper plane --}}
        <div class="absolute top-4 right-10 opacity-10 pointer-events-none select-none">
            <svg class="w-24 h-24" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M10,80 L80,20 L50,50 L10,80 Z" />
                <path d="M80,20 L50,50" />
                <path d="M50,50 L40,90 L60,65" />
            </svg>
        </div>

        {{-- Left: character and plane illustrations --}}
        <div class="flex items-center gap-6 select-none max-w-sm">
            <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-full border-4 border-white shadow-md overflow-hidden flex-shrink-0 relative">
                <img src="{{ asset('images/pakka_learns_1784465119731.jpg') }}" alt="Pakka Patriot reading"
                     class="w-full h-full object-cover" referrerpolicy="no-referrer">
            </div>

            <div class="text-left">
                <svg class="w-16 h-8 text-[#F6B828]" viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M10,20 Q40,5 60,15 T90,10" stroke-dasharray="3 3" />
                    <polygon points="90,10 82,6 85,12" fill="currentColor" />
                </svg>
                <h3 class="font-brush text-3xl sm:text-4xl text-[#0A2240] tracking-wide leading-none mt-2">
                    LET'S STAY <br>
                    <span class="text-[#F6B828]">IN TOUCH!</span>
                </h3>
                <p class="font-sans text-xs sm:text-sm text-[#6B5636] font-semibold mt-1">
                    Get stories, fun facts and updates straight to your inbox.
                </p>
            </div>
        </div>

        {{-- Right: subscription form (Livewire, with server-side validation) --}}
        <div class="w-full flex-grow max-w-lg lg:max-w-none">
            <livewire:newsletter-form />
        </div>
    </div>
</section>
