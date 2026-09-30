@extends('layouts.site')

@section('title', 'Pakka Patriot - Know Bhārat. Be Bhārat.')

@section('content')
    @include('partials.home.hero')
    @include('partials.home.cards')

    {{-- Latest stories (75%) beside the Today's Panchangam card (25%). --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 border-t border-[#F0EBE0]/60">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3">
                @include('partials.home.latest-stories')
            </div>
            <div class="lg:col-span-1">
                @include('partials.home.panchangam-card')
            </div>
        </div>
    </section>

    @include('partials.home.games')

    @include('partials.home.recent-columns')

    @include('partials.home.shop-section')
    @include('partials.home.newsletter')
@endsection
