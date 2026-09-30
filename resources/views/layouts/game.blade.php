<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Play — Pakka Patriot')</title>
    <meta name="description" content="@yield('description', 'Play a board game from ancient Bhārat.')">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#0A2240">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google tag (gtag.js), configured via GOOGLE_ANALYTICS_ID --}}
    @include('partials.analytics')

    @stack('head')
</head>
{{--
    The game pages render no site header or footer: the React shell hid them on
    /play/* so the board could fill the viewport.
--}}
<body class="h-dvh overflow-hidden bg-[#0A2240] font-sans antialiased">
    @yield('content')

    @stack('scripts')
</body>
</html>
