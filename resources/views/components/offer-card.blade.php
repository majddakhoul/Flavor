@props(['offer'])
<article class="dish reveal">
    <a class="dish__media" href="{{ route('offers.show', $offer) }}">
        <img src="{{ $offer->meals->first()?->image_url ?? asset('assets/img/meals/placeholder.svg') }}" alt="{{ $offer->t('title') }}" loading="lazy">
        <div class="dish__flags">
            <x-badge tone="brand">-{{ $offer->discount_amount }}%</x-badge>
            @unless ($offer->is_orderable)<x-badge tone="danger">{{ __('app.unavailable') }}</x-badge>@endunless
        </div>
    </a>
    <div class="dish__body">
        <h3 class="dish__title"><a href="{{ route('offers.show', $offer) }}">{{ $offer->t('title') }}</a></h3>
        <p class="dish__desc">{{ $offer->t('description') }}</p>
        <p class="small muted">{{ $offer->meals->count() }} {{ __('app.items_in_bundle') }} · {{ __('app.until') }} {{ $offer->end_date?->translatedFormat('d M') }}</p>
        <div class="dish__foot">
            <span>
                <span class="price">@money($offer->final_price)</span>
                <s class="small muted">@money($offer->price)</s>
            </span>
            @auth
                <form method="POST" action="{{ route('account.cart.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="offer">
                    <input type="hidden" name="id" value="{{ $offer->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn--sm" type="submit" @disabled(! $offer->is_orderable)>
                        <x-icon name="cart" /><span>{{ __('app.add') }}</span>
                    </button>
                </form>
            @else
                <a class="btn btn--sm btn--ghost" href="{{ route('login') }}">{{ __('app.sign_in_to_order') }}</a>
            @endauth
        </div>
    </div>
</article>
