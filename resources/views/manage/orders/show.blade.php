@extends('layouts.manage')

@section('title', $order->reference)
@section('eyebrow', __('app.section_sales'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <button class="btn btn--ghost btn--sm" type="button" onclick="window.print()"><x-icon name="print" />{{ __('app.print') }}</button>
@endsection

@section('content')
    <div class="split">
        <div class="stack">
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
                @if ($order->notes)
                    <p class="small muted" style="margin-top:var(--space-3)"><x-icon name="edit" /> {{ $order->notes }}</p>
                @endif
            </x-card>

            <x-card :title="__('app.change_status')">
                <form method="POST" action="{{ route('manage.orders.status', $order) }}" class="cluster">
                    @csrf
                    @method('PATCH')
                    <x-select name="status" :options="$statuses" :selected="$order->status->value" style="max-width:240px" />
                    <button class="btn" type="submit"><x-icon name="check" />{{ __('app.apply') }}</button>
                </form>
                <p class="field__hint" style="margin-top:var(--space-2)">{{ __('app.status_transition_hint') }}</p>
            </x-card>
        </div>

        <div class="stack">
            <div class="ticket">
                <p class="eyebrow">{{ __('app.kitchen_ticket') }}</p>
                <span class="ticket__code">{{ $order->reference }}</span>
                <hr class="ticket__rule">
                <dl style="margin:0">
                    <div class="ticket__row"><dt>{{ __('app.status') }}</dt><dd>{{ $order->status->label() }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.type') }}</dt><dd>{{ $order->order_type->label() }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.customer') }}</dt><dd>{{ $order->customer?->user?->full_name ?? '—' }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.handled_by') }}</dt><dd>{{ $order->employee?->user?->full_name ?? '—' }}</dd></div>
                    @if ($order->location)
                        <div class="ticket__row"><dt>{{ __('app.delivery_area') }}</dt><dd>{{ $order->location->label }}</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.estimated_delivery') }}</dt><dd>{{ $order->estimated_delivery }}</dd></div>
                    @endif
                    @if ($order->reservation)
                        <div class="ticket__row"><dt>{{ __('app.reservation') }}</dt><dd>{{ $order->reservation->reservation_code }}</dd></div>
                    @endif
                    <div class="ticket__row"><dt>{{ __('app.meals') }}</dt><dd>@money($order->meals_total)</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.offers') }}</dt><dd>@money($order->offers_total)</dd></div>
                    @if ($order->tables_total)<div class="ticket__row"><dt>{{ __('app.tables') }}</dt><dd>@money($order->tables_total)</dd></div>@endif
                </dl>
                <div class="ticket__total"><span>{{ __('app.total') }}</span><span class="price">@money($order->total)</span></div>
            </div>

            @can('delete', $order)
                <x-card :title="__('app.danger_zone')">
                    <p class="small muted">{{ __('app.purge_hint') }}</p>
                    <x-delete-form :action="route('manage.orders.destroy', $order->id)" :label="__('app.delete_permanently')" :confirm="__('app.confirm_purge')" />
                </x-card>
            @endcan
        </div>
    </div>
@endsection
