<div wire:key="journey-modal-root">
@if($open)
    <div class="fixed inset-0 z-40 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         wire:click.self="close" wire:key="journey-modal">
        <div class="relative bg-brand-cream max-w-md w-full rounded-3xl overflow-hidden shadow-2xl border border-[#F0EBE0] p-6 sm:p-8 flex flex-col text-left">

            <button type="button" wire:click="close"
                    class="absolute top-4 right-4 bg-black/5 hover:bg-black/10 text-[#0A2240] p-2.5 rounded-full transition-all duration-200"
                    title="Close Dialog">
                <x-pp-icon name="x" :size="16" />
            </button>

            @if($submitted)
                {{-- Success state: closes itself after three seconds. --}}
                <div class="py-8 text-center space-y-4" x-data x-init="setTimeout(() => $wire.resetForm(), 3000)">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto text-green-600 animate-bounce">
                        <x-pp-icon name="award" :size="32" />
                    </div>
                    <h3 class="font-display font-black text-2xl text-[#0A2240]">YOU'RE A PATRIOT BUDDY!</h3>
                    <p class="text-sm text-[#587760] font-semibold">
                        Woohoo! Welcome on board {{ $name }}. Your official buddy passport and sticker card is flying to your email inbox!
                    </p>
                    <div class="pt-4 border-t border-gray-200 text-xs text-gray-400">
                        Closing passport generator...
                    </div>
                </div>
            @else
                <form wire:submit="submit" class="space-y-5">
                    <div class="text-center sm:text-left select-none">
                        <span class="text-[10px] font-black tracking-widest text-[#F6B828] uppercase font-sans">EXPLORE REAL INDIA</span>
                        <h3 class="font-brush text-3xl sm:text-4xl text-[#0A2240] tracking-wide mt-1">
                            BECOME A <span class="text-[#F6B828]">BUDDY!</span>
                        </h3>
                        <p class="text-xs text-[#4E637A] font-semibold mt-1">
                            Get an official buddy identity card, free sticker packets, and local explorer puzzles sent to you!
                        </p>
                    </div>

                    <div class="space-y-4 pt-2">
                        <div>
                            <label for="journeyName" class="text-xs font-black text-[#0A2240] uppercase tracking-wider block mb-1">Your Name</label>
                            <input id="journeyName" wire:model="name" type="text" placeholder="e.g. Aarav Sharma"
                                   class="w-full bg-white border border-[#DCD3B5] px-4 py-3 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#F6B828] text-[#0A2240]">
                            @error('name') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="journeyEmail" class="text-xs font-black text-[#0A2240] uppercase tracking-wider block mb-1">Parent's / Your Email</label>
                            <input id="journeyEmail" wire:model="email" type="email" placeholder="e.g. aarav@gmail.com"
                                   class="w-full bg-white border border-[#DCD3B5] px-4 py-3 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#F6B828] text-[#0A2240]">
                            @error('email') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="journeyAge" class="text-xs font-black text-[#0A2240] uppercase tracking-wider block mb-1">Age</label>
                                <input id="journeyAge" wire:model="age" type="number" placeholder="e.g. 12"
                                       class="w-full bg-white border border-[#DCD3B5] px-4 py-3 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#F6B828] text-[#0A2240]">
                                @error('age') <span class="text-xs font-bold text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="journeyCity" class="text-xs font-black text-[#0A2240] uppercase tracking-wider block mb-1">City</label>
                                <input id="journeyCity" wire:model="city" type="text" placeholder="e.g. Jaipur"
                                       class="w-full bg-white border border-[#DCD3B5] px-4 py-3 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#F6B828] text-[#0A2240]">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-xs font-black text-[#0A2240] uppercase tracking-wider block">What excites you most?</span>
                            <div class="flex flex-wrap gap-1.5 pt-1 select-none">
                                @foreach($interestOptions as $interest)
                                    @php $active = in_array($interest, $interests, true); @endphp
                                    <button type="button" wire:key="interest-{{ md5($interest) }}"
                                            wire:click="toggleInterest(@js($interest))"
                                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer
                                                   {{ $active
                                                        ? 'bg-[#F6B828] border-[#F6B828] text-white'
                                                        : 'bg-white border-[#DCD3B5] text-[#0A2240] hover:bg-gray-50' }}">
                                        {{ $interest }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                            class="w-full bg-[#F6B828] hover:bg-[#DAA520] text-white py-3.5 rounded-xl font-bold text-sm shadow hover:shadow-lg transition-all flex items-center justify-center gap-1.5 mt-4 disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-pp-icon name="trophy" :size="16" />
                        <span wire:loading.remove wire:target="submit">CLAIM BUDDY PASSPORT</span>
                        <span wire:loading wire:target="submit">SAVING...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
@endif
</div>
