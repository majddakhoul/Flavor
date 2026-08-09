@extends('layouts.manage')

@section('title', __('app.stock_report'))
@section('eyebrow', __('app.section_reports'))

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.stock_value')" :value="\App\Support\Money::format($snapshot['value'])" icon="money" />
        <x-stat :label="__('app.low_stock')" :value="$snapshot['low']" icon="alert" :tone="$snapshot['low'] > 0 ? 'danger' : 'brand'" />
        <x-stat :label="__('app.active')" :value="$snapshot['active']" icon="check" tone="teal" />
        <x-stat :label="__('app.ingredients')" :value="$snapshot['total']" icon="box" tone="info" />
    </div>

    <x-card :title="__('app.stock_watchlist')">
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.ingredient') }}</th>
                    <th>{{ __('app.stock') }}</th>
                    <th>{{ __('app.unit_cost') }}</th>
                    <th>{{ __('app.stock_value') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($snapshot['watchlist'] as $ingredient)
                    <tr>
                        <td><b>{{ $ingredient->t('name') }}</b></td>
                        <td><x-badge :tone="$ingredient->stock_quantity <= config('flavor.inventory.critical_stock_threshold') ? 'danger' : 'warning'">
                            {{ $ingredient->stock_quantity }} {{ $ingredient->t('unit') }}</x-badge></td>
                        <td class="mono">@money($ingredient->unit_cost)</td>
                        <td class="price">@money($ingredient->stock_value)</td>
                        <td>
                            <div class="table__actions">
                                <form method="POST" action="{{ route('manage.ingredients.stock', $ingredient) }}" class="cluster">
                                    @csrf @method('PATCH')
                                    <input class="input" style="max-width:110px" type="number" min="0" name="stock_quantity" value="{{ $ingredient->stock_quantity }}">
                                    <input type="hidden" name="reason" value="{{ __('app.restock_from_report') }}">
                                    <button class="btn btn--sm" type="submit"><x-icon name="refresh" />{{ __('app.restock') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state :title="__('app.stock_healthy')" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
