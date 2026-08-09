@extends('layouts.manage')

@section('title', $offer->t('title'))
@section('eyebrow', __('app.offers'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.offers.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <a class="btn btn--ghost btn--sm" href="{{ route('offers.show', $offer) }}"><x-icon name="eye" />{{ __('app.view') }}</a>
    <x-delete-form :action="route('manage.offers.destroy', $offer)" :label="__('app.delete')" />
@endsection

@section('content')
    <div class="split">
        <x-card :title="__('app.details')">
            <form method="POST" action="{{ route('manage.offers.update', $offer) }}">
                @csrf
                @method('PUT')
                @include('manage.offers._form')
                <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
            </form>
        </x-card>

        <div class="stack">
            <div class="ticket">
                <p class="eyebrow">{{ __('app.bundle_ticket') }}</p>
                <span class="ticket__code">OFR-{{ str_pad($offer->id, 4, '0', STR_PAD_LEFT) }}</span>
                <hr class="ticket__rule">
                <dl style="margin:0">
                    <div class="ticket__row"><dt>{{ __('app.list_price') }}</dt><dd>@money($offer->price)</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.you_save') }}</dt><dd>@money($offer->discount_margin)</dd></div>
                    <div class="ticket__row"><dt>{{ __('app.stock') }}</dt><dd>{{ $offer->in_stock ? __('app.available_now') : __('app.sold_out') }}</dd></div>
                </dl>
                <div class="ticket__total"><span>{{ __('app.final_price') }}</span><span class="price">@money($offer->final_price)</span></div>
            </div>

            <x-card :title="__('app.current_items')">
                @forelse ($offer->meals as $meal)
                    <div class="between" style="padding:.35rem 0">
                        <span>{{ $meal->t('name') }} <span class="small muted">×{{ $meal->pivot->quantity }}</span></span>
                        <x-delete-form :action="route('manage.offers.meals.detach', [$offer, $meal])" :confirm="__('app.confirm_remove_item')" />
                    </div>
                @empty
                    <p class="small muted">{{ __('app.no_items_yet') }}</p>
                @endforelse
            </x-card>
        </div>
    </div>
@endsection
