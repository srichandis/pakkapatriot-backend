@extends('layouts.site')

@section('title', 'About Us — Pakka Patriot')
@section('description', 'A movement to rekindle the spirit of active citizenship — where everyone discovers the power of Bhārat they hold in shaping the nation’s story.')

@php
    $qualities = [
        ['icon' => 'heart', 'iconClass' => 'text-[#F6B828]', 'title' => 'Loves Bhārat', 'description' => 'A deep, genuine affection for the country — its people, its land, and its stories.'],
        ['icon' => 'book-open', 'iconClass' => 'text-brand-sage', 'title' => 'Stays Curious', 'description' => "Always learning about Bhārat's rich heritage, diverse cultures, and incredible innovations."],
        ['icon' => 'compass', 'iconClass' => 'text-brand-blue', 'title' => 'Explores Fearlessly', 'description' => 'Steps off the beaten path to discover the real Bhārat — from hidden villages to forgotten histories.'],
        ['icon' => 'shield', 'iconClass' => 'text-[#F6B828]', 'title' => 'Takes Responsibility', 'description' => "Understands that citizenship is not passive — every action shapes the nation's future."],
        ['icon' => 'globe', 'iconClass' => 'text-emerald-500', 'title' => 'Celebrates Diversity', 'description' => "Embraces Bhārat's pluralism as its greatest strength — many cultures, one nation."],
        ['icon' => 'lightbulb', 'iconClass' => 'text-amber-500', 'title' => 'Creates Change', 'description' => 'Believes in the power of ideas to build a better tomorrow for all people of Bhārat.'],
    ];

    $pillars = [
        ['id' => 'learns', 'icon' => 'book-open', 'title' => 'PAKKA LEARNS', 'subtitle' => "Timeless wisdom from Bhārat's past and present — history, science, art, and philosophy.", 'bg' => 'bg-brand-sage', 'border' => 'border-brand-yellow', 'href' => route('collection.browse', 'ideas')],
        ['id' => 'explores', 'icon' => 'compass', 'title' => 'PAKKA EXPLORES', 'subtitle' => 'Incredible places, hidden gems, and the breathtaking diversity of landscapes of Bhārat.', 'bg' => 'bg-brand-blue', 'border' => 'border-brand-orange', 'href' => route('collection.browse', 'places')],
        ['id' => 'celebrates', 'icon' => 'award', 'title' => 'PAKKA CELEBRATES', 'subtitle' => 'Festivals, traditions, art forms, and the joyful spirit that defines culture of Bhārat.', 'bg' => 'bg-[#F6B828]', 'border' => 'border-brand-yellow', 'href' => route('collection.browse', 'culture')],
        ['id' => 'creates', 'icon' => 'lightbulb', 'title' => 'PAKKA CREATES', 'subtitle' => 'Innovation, entrepreneurship, and ideas that shape a brighter future for the nation.', 'bg' => 'bg-[#E8A817]', 'border' => 'border-brand-orange', 'href' => url('/create')],
    ];

    $promise = [
        ['icon' => 'smile', 'iconClass' => 'text-brand-sage', 'text' => "Free access to 80+ eBooks on Bhārat's freedom fighters, poets, scientists, and saints"],
        ['icon' => 'sparkles', 'iconClass' => 'text-[#F6B828]', 'text' => 'Inspiring stories of integrity, diversity, and local heroes from across Bhārat'],
        ['icon' => 'star', 'iconClass' => 'text-[#F6B828]', 'text' => 'A growing community of Pakka Patriots who believe in building a better Bhārat'],
        ['icon' => 'globe', 'iconClass' => 'text-brand-blue', 'text' => 'Resources that celebrate Made in Bhārat products and homegrown innovation'],
    ];

    $timeline = [
        ['year' => '2024 — The Idea', 'dot' => 'bg-brand-blue', 'text' => "Born from a simple question: How can we make Bhārat's incredible stories, heritage, and wisdom accessible to every curious young mind?"],
        ['year' => '2025 — Building the Movement', 'dot' => 'bg-[#F6B828]', 'text' => 'What started as a collection of stories grew into a full-fledged platform — with curated content, merchandise celebrating culture of Bhārat, and a growing community.'],
        ['year' => '2026 — A Nation of Patriots', 'dot' => 'bg-[#F6B828]', 'text' => 'Today, Pakka Patriot is a thriving ecosystem of stories, products, and experiences — empowering thousands of young people of Bhārat to know Bhārat and be Bhārat.'],
    ];
@endphp

@section('content')
    <section id="about" class="bg-brand-cream relative overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-[#FBECE6]/40 to-transparent pointer-events-none"></div>
        <div class="absolute top-20 right-10 w-48 h-48 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-40 left-10 w-64 h-64 bg-[#F6B828]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 relative z-10">

            {{-- Hero --}}
            <div class="text-center max-w-3xl mx-auto mb-20">
                <div class="flex items-center justify-center gap-3 mb-6 select-none">
                    <div class="w-8 h-px bg-[#F6B828]"></div>
                    <x-pp-icon name="star" :size="20" fill="#F6B828" class="text-[#F6B828]" />
                    <div class="w-8 h-px bg-[#F6B828]"></div>
                </div>

                <h1 class="font-brush text-5xl sm:text-6xl lg:text-7xl text-brand-blue tracking-wide leading-tight mb-4">
                    We are <span class="text-[#F6B828]">Pakka Patriot</span>
                </h1>
                <p class="font-sans text-lg sm:text-xl text-[#4E637A] font-medium leading-relaxed max-w-2xl mx-auto">
                    A movement to rekindle the spirit of active citizenship — where every discovers
                    the power of Bhārat they hold in shaping the nation's story.
                </p>
                <div class="mt-6 inline-block bg-white rounded-full px-6 py-2 border border-[#F0EBE0] shadow-sm">
                    <span class="font-display font-black text-sm tracking-widest text-brand-blue">
                        KNOW INDIA. <span class="text-[#F6B828]">BE INDIA.</span>
                    </span>
                </div>
            </div>

            {{-- Our mission --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-24">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5">
                        <x-pp-icon name="target" :size="16" class="text-[#F6B828]" />
                        <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Our Mission</span>
                    </div>
                    <h2 class="font-display font-bold text-3xl sm:text-4xl text-brand-blue leading-tight">
                        Turning <span class="text-[#F6B828]">indifference</span> into <span class="text-brand-sage">action</span>
                    </h2>
                    <div class="space-y-4 text-[#4E637A] leading-relaxed">
                        <p class="font-semibold">
                            Pakka Patriot exists to inspire everyday citizens to transition of Bhārat from being
                            indifferent observers into responsible, active participants in Bhārat's democracy.
                        </p>
                        <p class="font-medium">
                            We believe that real patriotism isn't about grand gestures — it's the daily commitment
                            to unity, harmony, and peace. It's about recognizing that each of us holds the power
                            to shape our nation's future through our choices, our voice, and our actions.
                        </p>
                        <p class="font-medium">
                            Through stories, resources, and a growing community of like-minded citizens, we guide
                            fellow people of Bhārat on a journey of self-realization — creating a society where shared
                            responsibility and democratic values aren't just ideals, but a way of life.
                        </p>
                    </div>
                </div>

                <div class="relative flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute -top-6 -right-6 w-32 h-32 bg-[#F6B828]/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-[#F6B828]/10 rounded-full blur-xl pointer-events-none"></div>

                        <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-xl p-8 space-y-6">
                            <div class="flex items-center gap-4 pb-4 border-b border-[#F0EBE0]">
                                <div class="w-14 h-14 rounded-2xl bg-[#FBECE6] flex items-center justify-center">
                                    <x-pp-icon name="heart" :size="28" fill="#F6B828" class="text-[#F6B828]" />
                                </div>
                                <div>
                                    <h3 class="font-display font-black text-lg text-brand-blue">Our Promise</h3>
                                    <p class="text-sm text-[#8A9EB4] font-semibold">To every curious person of Bhārat</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach($promise as $item)
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex-shrink-0">
                                            <x-pp-icon :name="$item['icon']" :size="20" class="{{ $item['iconClass'] }}" />
                                        </div>
                                        <p class="text-sm font-semibold text-[#2F445A]">{{ $item['text'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Our pillars --}}
            <div class="mb-24">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5 mb-4">
                        <x-pp-icon name="compass" :size="16" class="text-[#F6B828]" />
                        <span class="text-xs font-black tracking-widest text-brand-blue uppercase">What We Do</span>
                    </div>
                    <h2 class="font-brush text-4xl sm:text-5xl text-brand-blue tracking-wide">
                        The <span class="text-[#F6B828]">Pakka Patriot</span> Way
                    </h2>
                    <p class="text-[#4E637A] font-semibold mt-3 max-w-xl mx-auto">
                        Four pillars that guide everything we create — from stories to products to experiences.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($pillars as $pillar)
                        <a href="{{ $pillar['href'] }}"
                           class="{{ $pillar['bg'] }} rounded-3xl p-6 sm:p-8 {{ $pillar['border'] }} border-b-4 shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-5">
                                <x-pp-icon :name="$pillar['icon']" :size="32" class="text-white" />
                            </div>
                            <h3 class="font-display font-black text-lg text-white mb-2 tracking-wider">
                                {{ $pillar['title'] }}
                            </h3>
                            <p class="text-sm text-white/80 font-medium leading-relaxed">
                                {{ $pillar['subtitle'] }}
                            </p>
                            <span class="inline-flex items-center gap-1 mt-4 text-[11px] font-black uppercase tracking-widest text-white/70 group-hover:text-white transition-colors">
                                Explore
                                <x-pp-icon name="arrow-right" :size="13" class="transition-transform group-hover:translate-x-1" />
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- What makes a Pakka Patriot --}}
            <div class="mb-24">
                <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-brand-blue to-[#1A3A5C] p-8 sm:p-10 text-center">
                        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1.5 mb-4">
                            <x-pp-icon name="award" :size="16" class="text-[#F6B828]" />
                            <span class="text-xs font-black tracking-widest text-white uppercase">Our Philosophy</span>
                        </div>
                        <h2 class="font-brush text-4xl sm:text-5xl text-white tracking-wide">
                            What Makes a <span class="text-[#F6B828]">Pakka Patriot</span>?
                        </h2>
                        <p class="text-[#B5CADF] font-semibold mt-3 max-w-2xl mx-auto">
                            Being a Pakka Patriot isn't about where you were born — it's about the choices you make
                            every single day. Here's what sets a true patriot apart.
                        </p>
                    </div>

                    <div class="p-6 sm:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($qualities as $quality)
                                <div class="flex gap-4 p-5 rounded-2xl bg-brand-cream border border-[#F0EBE0] hover:border-[#F6B828]/30 hover:shadow-md transition-all duration-200 group">
                                    <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                                        <x-pp-icon :name="$quality['icon']" :size="24" class="{{ $quality['iconClass'] }}" />
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="font-display font-bold text-brand-blue text-sm">{{ $quality['title'] }}</h3>
                                        <p class="text-xs text-[#4E637A] font-medium leading-relaxed">{{ $quality['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Our story --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-24">
                <div class="order-2 lg:order-1 relative flex justify-center">
                    <div class="relative w-full max-w-md">
                        <div class="absolute -top-4 -left-4 w-20 h-20 bg-[#F6B828]/10 rounded-full blur-xl pointer-events-none"></div>

                        <div class="bg-white rounded-3xl border border-[#F0EBE0] shadow-xl p-8 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#FBECE6] flex items-center justify-center">
                                    <x-pp-icon name="sparkles" :size="20" class="text-[#F6B828]" />
                                </div>
                                <span class="text-xs font-black tracking-widest text-brand-sage uppercase">The Beginning</span>
                            </div>

                            <div class="space-y-4 pl-2 border-l-2 border-[#F0EBE0]">
                                @foreach($timeline as $entry)
                                    <div class="relative pl-6">
                                        <div class="absolute left-[-9px] top-1.5 w-4 h-4 rounded-full {{ $entry['dot'] }} border-2 border-white shadow-sm"></div>
                                        <p class="text-sm font-bold text-brand-blue">{{ $entry['year'] }}</p>
                                        <p class="text-xs text-[#4E637A] font-medium mt-1">{{ $entry['text'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-2 border-t border-[#F0EBE0]">
                                <p class="text-xs text-[#8A9EB4] font-semibold italic">
                                    "The journey is just beginning. And we want you to be part of it."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 bg-brand-blue/5 rounded-full px-4 py-1.5">
                        <x-pp-icon name="star" :size="16" class="text-[#F6B828]" />
                        <span class="text-xs font-black tracking-widest text-brand-blue uppercase">Our Story</span>
                    </div>
                    <h2 class="font-display font-bold text-3xl sm:text-4xl text-brand-blue leading-tight">
                        From a <span class="text-[#F6B828]">spark</span> to a <span class="text-brand-sage">movement</span>
                    </h2>
                    <div class="space-y-4 text-[#4E637A] leading-relaxed">
                        <p class="font-semibold">
                            Pakka Patriot was created with a singular vision — to help every child and young of Bhārat adult
                            discover the richness of their own country. In a world of global content, we wanted to create
                            a space that celebrates what makes Bhārat truly special.
                        </p>
                        <p class="font-medium">
                            We started by asking young people what they knew about Bhārat beyond the textbooks. The answers
                            inspired us — and also showed us how much more there was to explore. From the unsung heroes of
                            the freedom struggle to the hidden villages practicing centuries-old crafts, Bhārat's story is
                            endless, and we're just getting started telling it.
                        </p>
                        <p class="font-medium">
                            Today, we're a community of learners, explorers, and dreamers who believe that when you truly
                            know Bhārat, you naturally want to be Bhārat — in every thought, every action, every day.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Join the movement --}}
            <div class="text-center">
                <div class="bg-gradient-to-br from-brand-blue to-[#1A3A5C] rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-[#F6B828]/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                        <div class="flex justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-[#F6B828] flex items-center justify-center shadow-lg">
                                <x-pp-icon name="users" :size="32" class="text-white" />
                            </div>
                        </div>

                        <h2 class="font-brush text-4xl sm:text-5xl text-white tracking-wide">
                            Join the <span class="text-[#F6B828]">Movement</span>
                        </h2>

                        <p class="text-[#B5CADF] font-semibold text-lg max-w-xl mx-auto">
                            Every great journey begins with a single step. Take yours today and become a Pakka Patriot.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                            <button type="button" data-open-journey
                                    class="bg-[#F6B828] hover:bg-[#DAA520] text-white px-8 py-4 rounded-xl text-md font-bold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 group">
                                BECOME A BUDDY
                                <x-pp-icon name="arrow-right" :size="18" class="transition-transform group-hover:translate-x-1" />
                            </button>
                            {{-- TODO: point at the Blade /shop page once the shop pass lands. --}}
                            <a href="{{ url('/shop') }}"
                               class="border-2 border-white/30 hover:bg-white/10 text-white px-8 py-4 rounded-xl text-md font-bold transition-all duration-200">
                                EXPLORE MERCH
                            </a>
                        </div>

                        <div class="pt-6 flex items-center justify-center gap-6 text-xs text-[#8EA6C0] font-semibold">
                            <span>🇮🇳 Free Resources</span>
                            <span class="w-1 h-1 rounded-full bg-[#8EA6C0]"></span>
                            <span>📚 80+ eBooks</span>
                            <span class="w-1 h-1 rounded-full bg-[#8EA6C0]"></span>
                            <span>🎯 Community</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
