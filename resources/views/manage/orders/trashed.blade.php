@extends('layouts.manage')

@section('title', __('app.cancelled_orders'))
@section('eyebrow', __('app.section_sales'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.orders.index') }}"><x-icon name="back" />{{ __('app.orders') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.orders.trashed')" :sorts="['dated_at' => __('app.date')]" />

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.reference') }}</th>
                    <th>{{ __('app.customer') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th>{{ __('app.cancelled_at') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="mono">{{ $order->reference }}</td>
                        <td>{{ $order->customer?->user?->full_name ?? '—' }}</td>
                        <td><x-status-badge :status="$order->status" /></td>
                        <td class="small mono">{{ $order->deleted_at?->translatedFormat('d M Y H:i') }}</td>
                        <td>
                            <div class="table__actions">
                                <form method="POST" action="{{ route('manage.orders.restore', $order->id) }}" data-confirm="{{ __('app.confirm_restore') }}">
                                    @csrf
                                    <button class="btn btn--ghost btn--sm" type="submit"><x-icon name="refresh" />{{ __('app.restore') }}</button>
                                </form>
                                <x-delete-form :action="route('manage.orders.destroy', $order->id)" :confirm="__('app.confirm_purge')" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state illustration="empty-cart" :title="__('app.nothing_here')" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </x-card>
@endsection
