@extends('layouts.account')

@section('title', __('app.checkout'))

@section('content')
    <x-card :title="__('app.delivery_details')">
        <form method="POST" action="{{ route('account.checkout.store') }}">
            @csrf
            <x-field name="location_id" :label="__('app.delivery_area')" :hint="__('app.delivery_area_hint')">
                <x-select name="location_id" :options="$locations" :selected="auth()->user()->location_id" :placeholder="__('app.choose')" />
            </x-field>

            <x-field name="notes" :label="__('app.order_notes')" :hint="__('app.order_notes_hint')">
                <x-textarea name="notes" rows="3" />
            </x-field>

            <button class="btn btn--block" type="submit"><x-icon name="check" />{{ __('app.place_order') }}</button>
        </form>
    </x-card>

    <div class="ticket">
        <p class="eyebrow">{{ __('app.order_summary') }}</p>
        <span class="ticket__code">{{ __('app.pending') }}</span>
        <hr class="ticket__rule">
        <dl style="margin:0">
            @foreach ($cart['lines'] as $line)
                <div class="ticket__row"><dt>{{ $line['quantity'] }} × {{ $line['label'] }}</dt><dd>@money($line['subtotal'])</dd></div>
            @endforeach
        </dl>
        <div class="ticket__total"><span>{{ __('app.total') }}</span><span class="price">@money($cart['total'])</span></div>
    </div>
@endsection
