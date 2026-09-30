<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pakka Patriot - Know Bhārat. Be Bhārat.')</title>
    <meta name="description" content="@yield('description', 'Pakka Patriot is your buddy on a journey to explore the real Bhārat — its stories, traditions, people and so much more!')">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#0A2240">

    {{-- Base URL of the Laravel JSON API consumed by the front-end scripts --}}
    <meta name="api-base" content="{{ url('/api') }}">

    {{-- Self-hosted fonts: preloaded so they render on first paint (no font flash) --}}
    <link rel="preload" href="{{ asset('fonts/Caveat-Brush.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Inter.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/JetBrains-Mono.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Space-Grotesk.woff2') }}" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    {{-- Google tag (gtag.js), configured via GOOGLE_ANALYTICS_ID --}}
    @include('partials.analytics')

    @stack('head')
</head>
<body class="min-h-screen bg-brand-cream relative flex flex-col font-sans antialiased">
    @include('partials.header')

    <main class="flex-grow">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Interactive overlays, owned by Livewire components --}}
    <livewire:cart-drawer />
    <livewire:product-modal />
    <livewire:join-journey-form />

    @include('partials.video-modal')

    @stack('modals')
    @stack('scripts')

    @livewireScripts
</body>
</html>
