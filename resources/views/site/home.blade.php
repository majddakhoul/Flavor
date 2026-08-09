@extends('layouts.site')

@section('title', __('app.home'))

@section('content')
    <section class="hero">
        <div class="hero__bg" aria-hidden="true"></div>
        <div class="shell hero__grid">
            <div>
                <p class="eyebrow">{{ __('app.tagline_short') }}</p>
                <h1>{!! __('app.hero_title') !!}</h1>
                <p class="lede">{{ __('app.hero_body') }}</p>
                <div class="cluster" style="margin-top:var(--space-3)">
                    <a class="btn" href="{{ route('menu.index') }}"><x-icon name="menu-book" />{{ __('app.browse_menu') }}</a>
                    <a class="btn btn--ghost" href="{{ auth()->check() ? route('account.reservations.create') : route('login') }}">
                        <x-icon name="calendar" />{{ __('app.book_table') }}
                    </a>
                </div>
                <div class="hero__stats">
                    <div class="hero__stat"><b>{{ $featured->count() ? \App\Models\Meal::count() : 0 }}</b><span>{{ __('app.dishes') }}</span></div>
                    <div class="hero__stat"><b>{{ $offers->count() }}</b><span>{{ __('app.live_offers') }}</span></div>
                    <div class="hero__stat"><b>{{ \App\Models\Table::where('is_active', true)->count() }}</b><span>{{ __('app.tables') }}</span></div>
                </div>
            </div>
            <div class="hero__art">
                <img src="{{ asset('assets/img/illustrations/hero-service.svg') }}" alt="">
            </div>
        </div>
    </section>

    <section class="shell" style="margin-top:var(--space-5)">
        <div class="between">
            <div>
                <p class="eyebrow">{{ __('app.chef_picks') }}</p>
                <h2>{{ __('app.top_rated') }}</h2>
            </div>
            <a class="btn btn--ghost btn--sm" href="{{ route('menu.index') }}">{{ __('app.see_all') }}<x-icon name="arrow" /></a>
        </div>

        <div class="grid grid-3" style="margin-top:var(--space-3)">
            @forelse ($featured as $meal)
                <x-dish-card :meal="$meal" />
            @empty
                <x-empty-state :title="__('app.no_dishes')" :description="__('app.no_dishes_hint')" />
            @endforelse
        </div>
    </section>

    @if ($offers->isNotEmpty())
        <section class="shell" style="margin-top:var(--space-5)">
            <div class="between">
                <div>
                    <p class="eyebrow">{{ __('app.this_week') }}</p>
                    <h2>{{ __('app.running_offers') }}</h2>
                </div>
                <a class="btn btn--ghost btn--sm" href="{{ route('offers.index') }}">{{ __('app.see_all') }}<x-icon name="arrow" /></a>
            </div>
            <div class="grid grid-3" style="margin-top:var(--space-3)">
                @foreach ($offers as $offer)
                    <x-offer-card :offer="$offer" />
                @endforeach
            </div>
        </section>
    @endif

    <section class="shell" style="margin-top:var(--space-5)">
        <p class="eyebrow">{{ __('app.by_category') }}</p>
        <h2>{{ __('app.explore_menu') }}</h2>
        <div class="grid grid-4" style="margin-top:var(--space-3)">
            @foreach ($categories as $category)
                <a class="card reveal" href="{{ route('menu.index', ['filters[category_id]' => $category->id]) }}">
                    <span class="stat__icon"><x-icon name="menu-book" /></span>
                    <h3 style="margin-top:var(--space-2)">{{ $category->t('name') }}</h3>
                    <p class="small muted">{{ $category->meals->count() }} {{ __('app.dishes') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
