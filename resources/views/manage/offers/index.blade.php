@extends('layouts.manage')

@section('title', __('app.offers'))
@section('eyebrow', __('app.section_kitchen'))

@section('actions')
    <a class="btn" href="{{ route('manage.offers.create') }}"><x-icon name="plus" />{{ __('app.new_offer') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.offers.index')" :sorts="['discount_amount' => __('app.discount'), 'end_date' => __('app.ends')]">
            <x-filter-select name="is_active" :label="__('app.status')" :options="['1' => __('app.active'), '0' => __('app.inactive')]" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.offer') }}</th>
                    <th>{{ __('app.items') }}</th>
                    <th>{{ __('app.list_price') }}</th>
                    <th>{{ __('app.discount') }}</th>
                    <th>{{ __('app.final_price') }}</th>
                    <th>{{ __('app.window') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($offers as $offer)
                    <tr>
                        <td>
                            <b>{{ $offer->t('title') }}</b><br>
                            <x-badge :tone="$offer->is_running ? 'success' : 'muted'" plain>{{ $offer->is_running ? __('app.running') : __('app.paused') }}</x-badge>
                        </td>
                        <td class="mono">{{ $offer->meals->sum('pivot.quantity') }}</td>
                        <td class="mono">@money($offer->price)</td>
                        <td class="mono">{{ $offer->discount_amount }}%</td>
                        <td class="price">@money($offer->final_price)</td>
                        <td class="small mono">{{ $offer->start_date?->format('d/m') }} — {{ $offer->end_date?->format('d/m') }}</td>
                        <td>
                            <div class="table__actions">
                                <form method="POST" action="{{ route('manage.offers.toggle', $offer) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn--ghost btn--sm" type="submit" title="{{ __('app.toggle') }}"><x-icon name="refresh" /></button>
                                </form>
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.offers.edit', $offer) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.offers.destroy', $offer)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <x-empty-state :title="__('app.no_offers')" :description="__('app.no_offers_hint')">
                            <a class="btn" href="{{ route('manage.offers.create') }}">{{ __('app.new_offer') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $offers->links() }}
    </x-card>
@endsection
