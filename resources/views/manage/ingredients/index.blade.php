@extends('layouts.manage')

@section('title', __('app.ingredients'))
@section('eyebrow', __('app.section_inventory'))

@section('actions')
    <a class="btn" href="{{ route('manage.ingredients.create') }}"><x-icon name="plus" />{{ __('app.new_ingredient') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.stock_value')" :value="\App\Support\Money::format($snapshot['value'])" icon="money" />
        <x-stat :label="__('app.low_stock')" :value="$snapshot['low']" icon="alert" :tone="$snapshot['low'] > 0 ? 'danger' : 'brand'" />
        <x-stat :label="__('app.active')" :value="$snapshot['active']" icon="check" tone="teal" />
        <x-stat :label="__('app.ingredients')" :value="$snapshot['total']" icon="box" tone="info" />
    </div>

    <x-card>
        <x-toolbar :action="route('manage.ingredients.index')" :sorts="['name' => __('app.name'), 'stock_quantity' => __('app.stock'), 'unit_cost' => __('app.unit_cost')]">
            <x-filter-select name="low_stock" :label="__('app.stock')" :options="['1' => __('app.low_stock'), '0' => __('app.healthy')]" />
            <x-filter-select name="is_active" :label="__('app.status')" :options="['1' => __('app.active'), '0' => __('app.inactive')]" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.name') }}</th>
                    <th>{{ __('app.stock') }}</th>
                    <th>{{ __('app.unit_cost') }}</th>
                    <th>{{ __('app.stock_value') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($ingredients as $ingredient)
                    <tr>
                        <td>{{ $ingredient->t('name') }}</td>
                        <td><x-badge :tone="$ingredient->is_low ? 'danger' : 'success'">{{ $ingredient->stock_quantity }} {{ $ingredient->t('unit') }}</x-badge></td>
                        <td><span class="price">@money($ingredient->unit_cost)</span></td>
                        <td><span class="mono">@money($ingredient->stock_value)</span></td>
                        <td><x-badge :tone="$ingredient->is_active ? 'success' : 'muted'">{{ $ingredient->is_active ? __('app.active') : __('app.inactive') }}</x-badge></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.ingredients.edit', $ingredient) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.ingredients.destroy', $ingredient)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state illustration="empty-box" :title="__('app.no_ingredients')" :description="__('app.no_ingredients_hint')">
                            <a class="btn" href="{{ route('manage.ingredients.create') }}">{{ __('app.new_ingredient') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $ingredients->links() }}
    </x-card>
@endsection
