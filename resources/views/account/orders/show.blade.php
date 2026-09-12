@extends('layouts.account')

@section('title', $order->reference)

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('account.orders.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    @can('revert', $order)
        <form method="POST" action="{{ route('account.orders.revert', $order) }}" data-confirm="{{ __('app.confirm_revert') }}">
            @csrf
            <button class="btn btn--ghost btn--sm" type="submit"><x-icon name="cart" />{{ __('app.move_back_to_cart') }}</button>
        </form>
    @endcan
    @can('cancel', $order)
        <x-delete-form :action="route('account.orders.cancel', $order)" :label="__('app.cancel_order')" :confirm="__('app.confirm_cancel_order')" />
    @endcan
@endsection

@section('content')
    <x-card :title="__('app.items')">
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.item') }}</th>
                    <th>{{ __('app.quantity') }}</th>
                    <th>{{ __('app.unit_price') }}</th>
                    <th>{{ __('app.subtotal') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->meals as $meal)
                    <tr>
                        <td class="cell-media"><img src="{{ $meal->image_url }}" alt="" data-lightbox="{{ $meal->t('name') }}"><span>{{ $meal->t('name') }}</span></td>
                        <td class="mono">{{ $meal->pivot->quantity }}</td>
                        <td>@money($meal->price)</td>
                        <td class="price">@money($meal->price * $meal->pivot->quantity)</td>
                    </tr>
                @endforeach
                @foreach ($order->offers as $offer)
                    <tr>
                        <td><x-badge tone="brand" plain>{{ __('app.offer') }}</x-badge> {{ $offer->t('title') }}</td>
                        <td class="mono">{{ $offer->pivot->quantity }}</td>
                        <td>@money($offer->final_price)</td>
                        <td class="price">@money($offer->final_price * $offer->pivot->quantity)</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="ticket">
        <p class="eyebrow">{{ __('app.order_ticket') }}</p>
        <span class="ticket__code">{{ $order->reference }}</span>
        <hr class="ticket__rule">
        <dl style="margin:0">
            <div class="ticket__row"><dt>{{ __('app.status') }}</dt><dd>{{ $order->status->label() }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.type') }}</dt><dd>{{ $order->order_type->label() }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.date') }}</dt><dd>{{ $order->dated_at?->translatedFormat('d M Y') }}</dd></div>
            @if ($order->location)
                <div class="ticket__row"><dt>{{ __('app.delivery_area') }}</dt><dd>{{ $order->location->label }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.estimated_delivery') }}</dt><dd>{{ $order->estimated_delivery }}</dd></div>
            @endif
            <div class="ticket__row"><dt>{{ __('app.meals') }}</dt><dd>@money($order->meals_total)</dd></div>
            <div class="ticket__row"><dt>{{ __('app.offers') }}</dt><dd>@money($order->offers_total)</dd></div>
            @if ($order->tables_total)<div class="ticket__row"><dt>{{ __('app.tables') }}</dt><dd>@money($order->tables_total)</dd></div>@endif
        </dl>
        <div class="ticket__total"><span>{{ __('app.total') }}</span><span class="price">@money($order->total)</span></div>
        @if ($order->notes)<p class="small muted" style="margin-top:var(--space-3)">{{ $order->notes }}</p>@endif
    </div>
@endsection
