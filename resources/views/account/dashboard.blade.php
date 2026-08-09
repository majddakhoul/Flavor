@extends('layouts.account')

@section('title', __('app.overview'))

@section('actions')
    <a class="btn btn--ghost" href="{{ route('menu.index') }}"><x-icon name="menu-book" />{{ __('app.browse_menu') }}</a>
    <a class="btn" href="{{ route('account.reservations.create') }}"><x-icon name="calendar" />{{ __('app.book_table') }}</a>
@endsection

@section('content')
    @if ($customer?->is_banned)
        <div class="card" style="border-inline-start:4px solid var(--color-danger)">
            <h3><x-icon name="alert" /> {{ __('app.account_restricted') }}</h3>
            <p class="muted">{{ __('app.ban_notice', ['date' => $customer->ban_until?->translatedFormat('d M Y')]) }}</p>
        </div>
    @endif

    <div class="grid grid-3">
        <x-stat :label="__('app.open_orders')" :value="$openOrders" icon="orders" />
        <x-stat :label="__('app.cart_items')" :value="$cartCount" icon="cart" tone="info" />
        <x-stat :label="__('app.upcoming_reservations')" :value="$reservations?->count() ?? 0" icon="calendar" tone="teal" />
    </div>

    <x-card :title="__('app.recent_orders')">
        <x-slot:action><a class="btn btn--ghost btn--sm" href="{{ route('account.orders.index') }}">{{ __('app.see_all') }}</a></x-slot:action>

        @forelse ($orders ?? [] as $order)
            <div class="between" style="padding:.6rem 0;border-bottom:1px solid var(--color-border)">
                <div>
                    <b class="mono">{{ $order->reference }}</b>
                    <p class="small muted" style="margin:0">{{ $order->order_type->label() }} · {{ $order->dated_at?->translatedFormat('d M Y') }}</p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$order->status" />
                    <span class="price">@money($order->total)</span>
                    <a class="btn btn--ghost btn--sm" href="{{ route('account.orders.show', $order) }}"><x-icon name="eye" /></a>
                </div>
            </div>
        @empty
            <x-empty-state illustration="empty-cart" :title="__('app.no_orders')" :description="__('app.no_orders_hint')">
                <a class="btn" href="{{ route('menu.index') }}">{{ __('app.browse_menu') }}</a>
            </x-empty-state>
        @endforelse
    </x-card>

    <x-card :title="__('app.upcoming_reservations')">
        @forelse ($reservations ?? [] as $reservation)
            <div class="between" style="padding:.6rem 0;border-bottom:1px solid var(--color-border)">
                <div>
                    <b class="mono">{{ $reservation->reservation_code }}</b>
                    <p class="small muted" style="margin:0">
                        {{ $reservation->starts_at?->translatedFormat('d M · H:i') }} — {{ $reservation->ends_at?->translatedFormat('H:i') }}
                        · {{ $reservation->tables->pluck('table_number')->implode(', ') }}
                    </p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$reservation->status" />
                    <a class="btn btn--ghost btn--sm" href="{{ route('account.reservations.show', $reservation) }}"><x-icon name="eye" /></a>
                </div>
            </div>
        @empty
            <x-empty-state illustration="empty-calendar" :title="__('app.no_reservations')" :description="__('app.no_reservations_hint')">
                <a class="btn" href="{{ route('account.reservations.create') }}">{{ __('app.book_table') }}</a>
            </x-empty-state>
        @endforelse
    </x-card>
@endsection
