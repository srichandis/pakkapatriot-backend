@extends('layouts.site')

@section('title', 'Shree Panchangam — Pakka Patriot')
@section('description', 'High-precision Drik Ganita panchangam — tithi, nakshatra, yoga and karana for today, at your location.')

@php
    /**
     * Temple Digital Panchangam.
     *
     * Ported from the React PanchangaPage. The page keeps that page's own dark
     * visual language (inline styles + a scoped style block) rather than the
     * brand palette, and the panchangam itself is computed in the browser by
     * resources/js/panchanga.js — see the note there about the calculation
     * library coming from a CDN.
     *
     * Fields marked data-panchanga-field are filled in by that script; until it
     * runs they show em dashes, exactly like the React page's loading state.
     */
    $fields = [
        ['samvatsara', 'Samvatsara | संवत्सरः'],
        ['ayana', 'Ayana | अयनम्'],
        ['maasa', 'Maasa | मासः'],
        ['paksha', 'Paksha | पक्षः'],
        ['tithi', 'Tithi | तिथिः'],
        ['vaara', 'Vaara | वारः'],
        ['nakshatra', 'Nakshatra | नक्षत्रम्'],
        ['yoga', 'Yoga | योगः'],
        ['karana', 'Karana | करणम्'],
    ];
@endphp

@push('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Mukta:wght@200;400;700&display=swap" rel="stylesheet">
    <style>
        .panchanga-root { background-color: #120907; color: #fefae0; font-family: 'Inter', system-ui, sans-serif; }
        .panchang-card {
            background: rgba(26, 12, 8, 0.75);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        .panchanga-fade-in { animation: panchangaFadeIn 1s ease-out both; }
        @keyframes panchangaFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .panchanga-label {
            display: block; font-size: 0.75rem; text-transform: uppercase; color: #FF9933;
            letter-spacing: 0.15em; margin-bottom: 4px; font-weight: 600;
        }
        .panchanga-value {
            display: block; font-size: 1.5rem; font-family: 'Mukta', system-ui, sans-serif;
            font-weight: 700; color: #D4AF37;
        }
        .panchanga-sub { display: block; font-size: 0.875rem; color: #e9edc9; font-style: italic; margin-top: -2px; }
    </style>
@endpush

@section('content')
<div id="panchangaRoot" data-panchanga-locate="auto" class="panchanga-root" style="min-height:100vh; width:100%; position:relative; overflow:hidden;">

    {{-- Background --}}
    <div style="position:fixed; inset:0; z-index:0;">
        <img src="{{ asset('panchanga/background.png') }}" alt=""
             style="width:100%; height:100%; object-fit:cover;">
        <div style="position:absolute; inset:0; background: radial-gradient(circle at center, transparent 0%, rgba(0,0,0,0.6) 100%);"></div>
    </div>

    <div style="position:relative; z-index:1; max-width:1280px; margin:0 auto; padding:32px 16px; min-height:100vh; display:flex; flex-direction:column;">

        {{-- Top bar --}}
        <header class="panchanga-fade-in" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px;">
            <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#b8c4d2; text-decoration:none;">
                <x-pp-icon name="arrow-left" :size="16" /> Home
            </a>
            <div style="display:flex; align-items:center; gap:6px; font-size:13px; color:#b8c4d2;">
                <x-pp-icon name="map-pin" :size="13" class="text-[#FF9933]" />
                <span id="panchangaLocation" style="font-weight:600;">Detecting location…</span>
            </div>
        </header>

        {{-- Title --}}
        <div class="panchanga-fade-in" style="text-align:center; margin-bottom:48px; animation-delay:0.1s;">
            <h1 style="font-size:clamp(2.5rem, 5vw, 3.5rem); font-weight:700; letter-spacing:0.05em; font-family:'Playfair Display', Georgia, serif; color:#D4AF37; text-shadow:0 4px 10px rgba(0,0,0,0.5); margin:0;">
                श्री पञ्चाङ्गम्
            </h1>
            <p style="font-size:clamp(1rem, 2vw, 1.25rem); margin-top:8px; letter-spacing:0.2em; font-family:'Mukta', system-ui, sans-serif; color:#FF9933; font-weight:400;">
                SHREE PANCHANGAM
            </p>
        </div>

        {{-- Clock --}}
        <div class="panchanga-fade-in" style="text-align:center; margin-bottom:48px; animation-delay:0.2s;">
            <div id="panchangaTime" style="font-size:clamp(3rem, 8vw, 5rem); font-weight:800; color:#fff; line-height:1; text-shadow:0 0 20px rgba(255,255,255,0.2);">--:--:--</div>
            <div id="panchangaDate" style="font-size:clamp(1rem, 2vw, 1.25rem); color:#e9edc9; margin-top:10px;">Loading…</div>
        </div>

        {{-- Error banner --}}
        <div id="panchangaError" style="display:none; background:rgba(180,40,40,0.85); color:#fff; padding:10px 20px; border-radius:12px; margin-bottom:24px; font-size:13px; font-weight:600; align-items:center; gap:8px;">
            <x-pp-icon name="alert-triangle" :size="16" />
        </div>

        {{-- Panchangam cards --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 420px), 1fr)); gap:28px; flex:1;">

            {{-- Card 1 — Samvatsara … Vaara --}}
            <div class="panchang-card panchanga-fade-in" style="animation-delay:0.3s;">
                <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:20px;">
                    @foreach(array_slice($fields, 0, 6) as [$key, $label])
                        <div data-panchanga-field="{{ $key }}" style="margin-bottom:12px;">
                            <span class="panchanga-label">{{ $label }}</span>
                            <span class="panchanga-value" data-panchanga-value>—</span>
                            <span class="panchanga-sub" data-panchanga-sub></span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Card 2 — Nakshatra, Yoga, Karana + sun times --}}
            <div class="panchang-card panchanga-fade-in" style="animation-delay:0.4s;">
                <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:20px; margin-bottom:32px;">
                    @foreach(array_slice($fields, 6) as [$key, $label])
                        <div data-panchanga-field="{{ $key }}" style="margin-bottom:12px;">
                            <span class="panchanga-label">{{ $label }}</span>
                            <span class="panchanga-value" data-panchanga-value>—</span>
                            <span class="panchanga-sub" data-panchanga-sub></span>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex; justify-content:space-between; padding-top:20px; border-top:1px solid rgba(255,255,255,0.1);">
                    @foreach([['sunrise', 'SUNRISE'], ['sunset', 'SUNSET']] as [$key, $label])
                        <div data-panchanga-field="{{ $key }}" style="text-align:center;">
                            <span style="display:block; font-size:0.8rem; color:#e9edc9; letter-spacing:0.15em; margin-bottom:4px;">{{ $label }}</span>
                            <span data-panchanga-value style="display:block; font-size:1.5rem; font-family:'Mukta', system-ui, sans-serif; font-weight:700; color:#fff;">--:--</span>
                            <span data-panchanga-sub></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="panchanga-fade-in" style="text-align:center; margin-top:48px; color:rgba(255,255,255,0.3); font-size:12px; animation-delay:0.6s;">
            High-precision Drik Ganita Calculations &bull; &copy; 2026 Temple Digital Services
        </footer>
    </div>
</div>
@endsection
