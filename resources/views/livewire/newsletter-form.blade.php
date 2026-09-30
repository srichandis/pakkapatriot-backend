<div class="w-full">
    @if($subscribed)
        <div class="bg-white border border-green-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
            <x-pp-icon name="check-circle-2" :size="40" class="text-green-500 animate-bounce" />
            <div>
                <p class="font-display font-bold text-[#0A2240] text-lg">Thank you for subscribing!</p>
                <p class="text-sm text-[#587760]">You've joined the loop. Standby for wonderful updates about Bhārat!</p>
            </div>
        </div>
    @else
        <form wire:submit="subscribe" class="w-full">
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                <div class="flex-grow relative">
                    <input type="email" wire:model="email" placeholder="Enter your email"
                           class="w-full bg-white border border-[#F0E2C8] text-[#0A2240] px-6 py-4 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#FAC644] placeholder-gray-400 shadow-sm
                                  @error('email') border-red-400 @enderror">
                    @error('email')
                        <span class="absolute -bottom-6 left-2 text-xs font-bold text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="subscribe"
                        class="bg-[#FECD55] hover:bg-[#FAC644] text-[#0A2240] font-black text-sm px-8 py-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow hover:shadow-md select-none disabled:opacity-60 disabled:cursor-not-allowed">
                    <x-pp-icon name="send" :size="16" />
                    <span wire:loading.remove wire:target="subscribe">SUBSCRIBE</span>
                    <span wire:loading wire:target="subscribe">SUBSCRIBING...</span>
                </button>
            </div>
        </form>
    @endif
</div>
