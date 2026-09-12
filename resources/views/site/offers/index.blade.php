@extends('layouts.site')

@section('title', __('app.offers'))

@section('content')
    <section class="shell" style="padding-block:var(--space-4)">
        <p class="eyebrow">{{ __('app.value_bundles') }}</p>
        <h1>{{ __('app.offers') }}</h1>
        <p class="lede">{{ __('app.offers_intro') }}</p>

        <x-toolbar :action="route('offers.index')" :sorts="['discount_amount' => __('app.discount'), 'end_date' => __('app.ends')]" />

        <div class="grid grid-cards">
            @forelse ($offers as $offer)
                <x-offer-card :offer="$offer" />
            @empty
                <x-empty-state :title="__('app.no_offers')" :description="__('app.no_offers_hint')" />
            @endforelse
        </div>

        {{ $offers->links() }}
    </section>
@endsection
