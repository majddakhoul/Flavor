@extends('layouts.site')

@section('title', $meal->t('name'))

@section('content')
    <section class="shell" style="padding-block:var(--space-4)">
        <a class="btn btn--ghost btn--sm" href="{{ route('menu.index') }}"><x-icon name="back" />{{ __('app.back_to_menu') }}</a>

        <div class="split" style="margin-top:var(--space-3)">
            <div class="stack">
                <img src="{{ $meal->image_url }}" alt="{{ $meal->t('name') }}" style="border-radius:var(--radius-lg);width:100%;max-height:420px;object-fit:cover">

                <div>
                    <p class="eyebrow">{{ $meal->category?->t('name') }}</p>
                    <h1>{{ $meal->t('name') }}</h1>
                    <p class="lede">{{ $meal->t('description') }}</p>
                </div>

                <div class="cluster">
                    <x-badge tone="brand" plain><x-icon name="clock" />{{ \Illuminate\Support\Str::of($meal->prep_time)->substr(0, 5) }}</x-badge>
                    @if ($meal->is_vegetarian)<x-badge tone="success"><x-icon name="leaf" />{{ __('app.vegetarian') }}</x-badge>@endif
                    <x-badge :tone="$meal->is_orderable ? 'success' : 'danger'">{{ $meal->is_orderable ? __('app.available_now') : __('app.sold_out') }}</x-badge>
                    <x-stars :value="$meal->rating_average" :count="$meal->ratings->count()" />
                </div>

                <x-card :title="__('app.ingredients')">
                    <ul style="list-style:none;margin:0;padding:0;display:grid;gap:.5rem">
                        @foreach ($meal->ingredients as $ingredient)
                            <li class="between" style="border-bottom:1px dashed var(--color-border);padding-bottom:.4rem">
                                <span>{{ $ingredient->t('name') }}</span>
                                <span class="mono small muted">{{ $ingredient->pivot->quantity }} {{ $ingredient->t('unit') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </x-card>

                @auth
                    @if (auth()->user()->isCustomer())
                        <x-card :title="__('app.rate_this_dish')">
                            <x-rating-form :action="route('account.ratings.meal', $meal)" />
                        </x-card>
                    @endif
                @endauth
            </div>

            <aside class="stack">
                <div class="ticket">
                    <p class="eyebrow">{{ __('app.order_ticket') }}</p>
                    <span class="ticket__code">{{ strtoupper(\Illuminate\Support\Str::substr($meal->t('name'), 0, 3)) }}-{{ str_pad($meal->id, 3, '0', STR_PAD_LEFT) }}</span>
                    <hr class="ticket__rule">
                    <dl style="margin:0">
                        <div class="ticket__row"><dt>{{ __('app.prep_cost') }}</dt><dd>@money($meal->prep_cost)</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.margin') }}</dt><dd>{{ $meal->percentage }}%</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.portions_left') }}</dt><dd>{{ $meal->max_portions }}</dd></div>
                    </dl>
                    <div class="ticket__total"><span>{{ __('app.price') }}</span><span class="price">@money($meal->price)</span></div>

                    @auth
                        <form method="POST" action="{{ route('account.cart.store') }}" class="stack" style="margin-top:var(--space-3)">
                            @csrf
                            <input type="hidden" name="type" value="meal">
                            <input type="hidden" name="id" value="{{ $meal->id }}">
                            <div class="qty" data-qty>
                                <button type="button" data-step="-1" aria-label="{{ __('app.decrease') }}">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $meal->max_portions) }}">
                                <button type="button" data-step="1" aria-label="{{ __('app.increase') }}">+</button>
                            </div>
                            <x-field name="notes" :label="__('app.kitchen_notes')">
                                <x-textarea name="notes" rows="2" />
                            </x-field>
                            <button class="btn btn--block" type="submit" @disabled(! $meal->is_orderable)>
                                <x-icon name="cart" />{{ __('app.add_to_cart') }}
                            </button>
                        </form>
                    @else
                        <a class="btn btn--block" style="margin-top:var(--space-3)" href="{{ route('login') }}">{{ __('app.sign_in_to_order') }}</a>
                    @endauth
                </div>

                <x-card :title="__('app.you_may_like')">
                    <div class="stack">
                        @foreach ($related->where('id', '!=', $meal->id)->take(3) as $item)
                            <a class="cluster" href="{{ route('menu.show', $item) }}">
                                <img src="{{ $item->image_url }}" alt="" style="width:56px;height:56px;border-radius:var(--radius-sm);object-fit:cover">
                                <span><b>{{ $item->t('name') }}</b><br><span class="price small">@money($item->price)</span></span>
                            </a>
                        @endforeach
                    </div>
                </x-card>
            </aside>
        </div>
    </section>
@endsection
