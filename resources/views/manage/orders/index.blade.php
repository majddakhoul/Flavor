@extends('layouts.manage')

@section('title', __('app.orders'))
@section('eyebrow', __('app.section_sales'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.trashed') }}"><x-icon name="trash" />{{ __('app.cancelled_orders') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.orders_today')" :value="$statistics['today']" icon="orders" />
        <x-stat :label="__('app.total_orders')" :value="$statistics['total']" icon="receipt" tone="info" />
        <x-stat :label="__('app.revenue_month')" :value="\App\Support\Money::format($statistics['revenue_month'])" icon="money" tone="teal" />
        <x-stat :label="__('app.pending')" :value="$statistics['by_status']['Pending'] ?? 0" icon="clock" />
    </div>

    <x-card>
        <x-toolbar :action="route('manage.orders.index')" :sorts="['dated_at' => __('app.date'), 'created_at' => __('app.newest')]">
            <x-filter-select name="status" :label="__('app.status')" :options="$statuses" />
            <x-filter-select name="order_type" :label="__('app.type')" :options="$types" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.reference') }}</th>
                    <th>{{ __('app.customer') }}</th>
                    <th>{{ __('app.type') }}</th>
                    <th>{{ __('app.items') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th>{{ __('app.total') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><a class="mono" href="{{ route('manage.orders.show', $order) }}">{{ $order->reference }}</a><br>
                            <span class="small muted">{{ $order->dated_at?->translatedFormat('d M Y') }}</span></td>
                        <td>{{ $order->customer?->user?->full_name ?? $order->employee?->user?->full_name ?? '—' }}</td>
                        <td>{{ $order->order_type->label() }}</td>
                        <td class="mono">{{ $order->items_count }}</td>
                        <td><x-status-badge :status="$order->status" /></td>
                        <td class="price">@money($order->total)</td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.show', $order) }}"><x-icon name="eye" /></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <x-empty-state illustration="empty-cart" :title="__('app.no_orders')" :description="__('app.no_orders_staff_hint')" />
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </x-card>
@endsection
