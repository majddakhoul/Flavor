@extends('layouts.site')

@section('title', $offer->t('title'))

@section('content')
    <section class="shell" style="padding-block:var(--space-4)">
        <a class="btn btn--ghost btn--sm" href="{{ route('offers.index') }}"><x-icon name="back" />{{ __('app.back_to_offers') }}</a>

        <div class="split" style="margin-top:var(--space-3)">
            <div class="stack">
                <div class="media-hero">
                    <img src="{{ $offer->image_url }}" alt="{{ $offer->t('title') }}" data-lightbox="{{ $offer->t('title') }}">
                </div>

                <div>
                    <p class="eyebrow">-{{ $offer->discount_amount }}% · {{ $offer->start_date?->translatedFormat('d M') }} — {{ $offer->end_date?->translatedFormat('d M') }}</p>
                    <h1>{{ $offer->t('title') }}</h1>
                    <p class="lede">{{ $offer->t('description') }}</p>
                    <x-stars :value="$offer->rating_average" :count="$offer->ratings->count()" />
                </div>

                <div class="grid grid-cards">
                    @foreach ($offer->meals as $meal)
                        <article class="card">
                            <span class="media-square">
                                <img src="{{ $meal->image_url }}" alt="{{ $meal->t('name') }}" data-lightbox="{{ $meal->t('name') }}">
                            </span>
                            <h3 style="margin-top:var(--space-2);font-size:1rem">{{ $meal->t('name') }}</h3>
                            <p class="small muted">×{{ $meal->pivot->quantity }} · @money($meal->price)</p>
                        </article>
                    @endforeach
                </div>

                @auth
                    @if (auth()->user()->isCustomer())
                        <x-card :title="__('app.rate_this_offer')">
                            <x-rating-form :action="route('account.ratings.offer', $offer)" />
                        </x-card>
                    @endif
                @endauth
            </div>

            <aside>
                <div class="ticket">
                    <p class="eyebrow">{{ __('app.bundle_ticket') }}</p>
                    <span class="ticket__code">OFR-{{ str_pad($offer->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <hr class="ticket__rule">
                    <dl style="margin:0">
                        <div class="ticket__row"><dt>{{ __('app.items') }}</dt><dd>{{ $offer->meals->sum('pivot.quantity') }}</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.list_price') }}</dt><dd>@money($offer->price)</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.you_save') }}</dt><dd>@money($offer->discount_margin)</dd></div>
                    </dl>
                    <div class="ticket__total"><span>{{ __('app.total') }}</span><span class="price">@money($offer->final_price)</span></div>

                    @auth
                        <form method="POST" action="{{ route('account.cart.store') }}" style="margin-top:var(--space-3)">
                            @csrf
                            <input type="hidden" name="type" value="offer">
                            <input type="hidden" name="id" value="{{ $offer->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="btn btn--block" type="submit" @disabled(! $offer->is_orderable)>
                                <x-icon name="cart" />{{ __('app.add_to_cart') }}
                            </button>
                        </form>
                    @else
                        <a class="btn btn--block" style="margin-top:var(--space-3)" href="{{ route('login') }}">{{ __('app.sign_in_to_order') }}</a>
                    @endauth
                </div>
            </aside>
        </div>
    </section>
@endsection
