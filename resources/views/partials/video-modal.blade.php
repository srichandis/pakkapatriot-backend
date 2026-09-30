<div id="videoModal" class="fixed inset-0 z-40 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="relative bg-black max-w-4xl w-full rounded-3xl overflow-hidden shadow-2xl border border-white/10 aspect-video flex flex-col">
        <button type="button" data-video-close
                class="absolute top-4 right-4 z-10 bg-white/10 hover:bg-white/20 text-white p-2.5 rounded-full transition-all duration-200"
                title="Close Video">
            <x-pp-icon name="x" :size="18" />
        </button>
        <iframe id="videoFrame" class="w-full h-full" src="" title="Incredible India Journey Video"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
    </div>
</div>
