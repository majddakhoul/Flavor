@extends('layouts.manage')

@section('title', $customer->user?->full_name)
@section('eyebrow', __('app.section_people'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.customers.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    @if ($customer->ban)
        <form method="POST" action="{{ route('manage.customers.unban', $customer) }}" data-confirm="{{ __('app.confirm_unban') }}">
            @csrf @method('PATCH')
            <button class="btn btn--sm" type="submit"><x-icon name="check" />{{ __('app.lift_ban') }}</button>
        </form>
    @else
        <form method="POST" action="{{ route('manage.customers.ban', $customer) }}" data-confirm="{{ __('app.confirm_ban') }}">
            @csrf @method('PATCH')
            <button class="btn btn--danger btn--sm" type="submit"><x-icon name="shield" />{{ __('app.ban_customer') }}</button>
        </form>
    @endif
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.orders')" :value="$customer->orders->count()" icon="orders" />
        <x-stat :label="__('app.reservations')" :value="$customer->reservations->count()" icon="calendar" tone="info" />
        <x-stat :label="__('app.cancelled')" :value="$customer->cancelledReservationsCount()" icon="close" tone="danger" />
        <x-stat :label="__('app.standing')" :value="$customer->is_banned ? __('app.banned') : __('app.good')" icon="shield" tone="teal" />
    </div>

    <div class="split">
        <x-card :title="__('app.recent_orders')">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr><th>{{ __('app.reference') }}</th><th>{{ __('app.date') }}</th><th>{{ __('app.status') }}</th><th>{{ __('app.total') }}</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($customer->orders->sortByDesc('created_at')->take(10) as $order)
                        <tr>
                            <td><a class="mono" href="{{ route('manage.orders.show', $order) }}">{{ $order->reference }}</a></td>
                            <td class="small">{{ $order->dated_at?->translatedFormat('d M Y') }}</td>
                            <td><x-status-badge :status="$order->status" /></td>
                            <td class="price">@money($order->total)</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted center">{{ __('app.no_orders') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="stack">
            <x-card :title="__('app.profile')">
                <dl style="margin:0">
                    <div class="ticket__row"><dt>{{ __('app.email') }}</dt><dd>{{ $customer->user?->email }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.phone') }}</dt><dd>{{ $customer->user?->phone }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.area') }}</dt><dd>{{ $customer->user?->location?->label ?? '—' }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.allergies') }}</dt><dd>{{ $customer->allergies?->label() ?? '—' }}</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.favorite_categories') }}</dt><dd>{{ $customer->favorite_categories ?? '—' }}</dd></div>
                    @if ($customer->ban_date)
                        <div class="ticket__row"><dt>{{ __('app.ban_date') }}</dt><dd>{{ $customer->ban_date->translatedFormat('d M Y') }}</dd></div>
                        <div class="ticket__row"><dt>{{ __('app.ban_until') }}</dt><dd>{{ $customer->ban_until?->translatedFormat('d M Y') }}</dd></div>
                    @endif
                </dl>
            </x-card>

            <x-card :title="__('app.reservations')">
                <ul class="timeline">
                    @forelse ($customer->reservations->sortByDesc('date')->take(6) as $reservation)
                        <li>
                            <a class="mono" href="{{ route('manage.reservations.show', $reservation) }}">{{ $reservation->reservation_code }}</a>
                            <p class="small muted" style="margin:0">{{ $reservation->date?->translatedFormat('d M Y') }} · {{ $reservation->status->label() }}</p>
                        </li>
                    @empty
                        <p class="small muted">{{ __('app.no_reservations') }}</p>
                    @endforelse
                </ul>
            </x-card>
        </div>
    </div>
@endsection
