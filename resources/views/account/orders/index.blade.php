@extends('layouts.account')

@section('title', __('app.my_orders'))

@section('content')
    <x-card :flush="false">
        <x-toolbar :action="route('account.orders.index')" :sorts="['dated_at' => __('app.date'), 'status' => __('app.status')]">
            <x-filter-select name="status" :label="__('app.status')" :options="\App\Enums\OrderStatus::options()" />
            <x-filter-select name="order_type" :label="__('app.type')" :options="\App\Enums\OrderType::options()" />
        </x-toolbar>

        @forelse ($orders as $order)
            <div class="between" style="padding:.75rem 0;border-bottom:1px solid var(--color-border)">
                <div>
                    <b class="mono">{{ $order->reference }}</b>
                    <p class="small muted" style="margin:0">
                        {{ $order->order_type->label() }} · {{ $order->dated_at?->translatedFormat('d M Y') }} · {{ $order->items_count }} {{ __('app.items') }}
                    </p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$order->status" />
                    <span class="price">@money($order->total)</span>
                    <a class="btn btn--ghost btn--sm" href="{{ route('account.orders.show', $order) }}"><x-icon name="eye" />{{ __('app.view') }}</a>
                </div>
            </div>
        @empty
            <x-empty-state illustration="empty-cart" :title="__('app.no_orders')" :description="__('app.no_orders_hint')">
                <a class="btn" href="{{ route('menu.index') }}">{{ __('app.browse_menu') }}</a>
            </x-empty-state>
        @endforelse

        {{ $orders->links() }}
    </x-card>
@endsection
