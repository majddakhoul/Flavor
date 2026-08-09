@extends('layouts.account')

@section('title', __('app.cart'))

@section('actions')
    @if ($cart['lines'])
        <form method="POST" action="{{ route('account.cart.clear') }}" data-confirm="{{ __('app.confirm_clear_cart') }}">
            @csrf @method('DELETE')
            <button class="btn btn--danger btn--sm" type="submit"><x-icon name="trash" />{{ __('app.clear_cart') }}</button>
        </form>
    @endif
@endsection

@section('content')
    @forelse ($cart['lines'] as $line)
        <x-card>
            <div class="between">
                <div class="cluster">
                    <img src="{{ $line['image'] }}" alt="" style="width:74px;height:74px;border-radius:var(--radius-sm);object-fit:cover">
                    <div>
                        <b>{{ $line['label'] }}</b>
                        <p class="small muted" style="margin:0">{{ $line['type']->label() }} · @money($line['unit_price'])</p>
                        @if ($line['notes'])<p class="small" style="margin:0"><x-icon name="edit" /> {{ $line['notes'] }}</p>@endif
                        @unless ($line['orderable'])<x-badge tone="danger">{{ __('app.sold_out') }}</x-badge>@endunless
                    </div>
                </div>

                <div class="cluster">
                    <form method="POST" action="{{ route('account.cart.update') }}" class="cluster">
                        @csrf @method('PUT')
                        <input type="hidden" name="type" value="{{ $line['type']->value }}">
                        <input type="hidden" name="id" value="{{ $line['id'] }}">
                        <input type="hidden" name="notes" value="{{ $line['notes'] }}">
                        <div class="qty" data-qty>
                            <button type="button" data-step="-1" aria-label="{{ __('app.decrease') }}">−</button>
                            <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="{{ $line['max'] }}">
                            <button type="button" data-step="1" aria-label="{{ __('app.increase') }}">+</button>
                        </div>
                        <button class="btn btn--ghost btn--sm" type="submit"><x-icon name="refresh" /></button>
                    </form>

                    <span class="price">@money($line['subtotal'])</span>

                    <x-delete-form :action="route('account.cart.destroy', [$line['type']->value, $line['id']])" :confirm="__('app.confirm_remove_item')" />
                </div>
            </div>
        </x-card>
    @empty
        <x-empty-state illustration="empty-cart" :title="__('app.cart_empty')" :description="__('app.cart_empty_hint')">
            <a class="btn" href="{{ route('menu.index') }}"><x-icon name="menu-book" />{{ __('app.browse_menu') }}</a>
        </x-empty-state>
    @endforelse

    @if ($cart['lines'])
        <div class="ticket">
            <p class="eyebrow">{{ __('app.order_summary') }}</p>
            <span class="ticket__code">{{ __('app.cart') }}</span>
            <hr class="ticket__rule">
            <dl style="margin:0">
                <div class="ticket__row"><dt>{{ __('app.meals') }}</dt><dd>@money($cart['meals_total'])</dd></div>
                <div class="ticket__row"><dt>{{ __('app.offers') }}</dt><dd>@money($cart['offers_total'])</dd></div>
                <div class="ticket__row"><dt>{{ __('app.items') }}</dt><dd>{{ $cart['count'] }}</dd></div>
            </dl>
            <div class="ticket__total"><span>{{ __('app.total') }}</span><span class="price">@money($cart['total'])</span></div>
            <a class="btn btn--block" style="margin-top:var(--space-3)" href="{{ route('account.checkout.create') }}">
                <x-icon name="receipt" />{{ __('app.checkout') }}
            </a>
        </div>
    @endif
@endsection
