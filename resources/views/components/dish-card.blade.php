@props(['meal'])
<article class="dish reveal">
    <a class="dish__media" href="{{ route('menu.show', $meal) }}">
        <img src="{{ $meal->image_url }}" alt="{{ $meal->t('name') }}" loading="lazy" data-lightbox="{{ $meal->t('name') }}">
        <div class="dish__flags">
            @if ($meal->is_vegetarian)<x-badge tone="success" plain><x-icon name="leaf" />{{ __('app.vegetarian') }}</x-badge>@endif
            @unless ($meal->is_orderable)<x-badge tone="danger">{{ __('app.sold_out') }}</x-badge>@endunless
        </div>
    </a>
    <div class="dish__body">
        <h3 class="dish__title"><a href="{{ route('menu.show', $meal) }}">{{ $meal->t('name') }}</a></h3>
        <p class="dish__desc">{{ $meal->t('description') ?: $meal->category?->t('name') }}</p>
        <div class="cluster small muted">
            <span class="cluster" style="gap:.25rem"><x-icon name="clock" />{{ \Illuminate\Support\Str::of($meal->prep_time)->substr(0, 5) }}</span>
            <x-stars :value="$meal->rating_average" />
        </div>
        <div class="dish__foot">
            <span class="price">@money($meal->price)</span>
            @auth
                <form method="POST" action="{{ route('account.cart.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="meal">
                    <input type="hidden" name="id" value="{{ $meal->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn--sm" type="submit" @disabled(! $meal->is_orderable)>
                        <x-icon name="cart" /><span>{{ __('app.add') }}</span>
                    </button>
                </form>
            @else
                <a class="btn btn--sm btn--ghost" href="{{ route('login') }}">{{ __('app.sign_in_to_order') }}</a>
            @endauth
        </div>
    </div>
</article>
