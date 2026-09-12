@extends('layouts.manage')

@section('title', __('app.meals'))
@section('eyebrow', __('app.section_kitchen'))

@section('actions')
    <a class="btn" href="{{ route('manage.meals.create') }}"><x-icon name="plus" />{{ __('app.new_meal') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.meals.index')" :sorts="['name' => __('app.name'), 'percentage' => __('app.margin'), 'created_at' => __('app.newest')]">
            <x-filter-select name="category_id" :label="__('app.category')" :options="$categories" />
            <x-filter-select name="availability" :label="__('app.availability')" :options="\App\Enums\MealAvailability::options()" />
            <x-filter-select name="vegetarian" :label="__('app.diet')" :options="['1' => __('app.vegetarian'), '0' => __('app.non_vegetarian')]" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.meal') }}</th>
                    <th>{{ __('app.category') }}</th>
                    <th>{{ __('app.prep_cost') }}</th>
                    <th>{{ __('app.margin') }}</th>
                    <th>{{ __('app.price') }}</th>
                    <th>{{ __('app.stock') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($meals as $meal)
                    <tr>
                        <td class="cell-media">
                            <img src="{{ $meal->image_url }}" alt="" data-lightbox="{{ $meal->t('name') }}">
                            <span>
                                <b>{{ $meal->t('name') }}</b><br>
                                <x-badge :tone="$meal->availability->tone() === 'success' ? 'success' : 'muted'" plain>{{ $meal->availability->label() }}</x-badge>
                            </span>
                        </td>
                        <td>{{ $meal->category?->t('name') }}</td>
                        <td class="mono">@money($meal->prep_cost)</td>
                        <td class="mono">{{ $meal->percentage }}%</td>
                        <td class="price">@money($meal->price)</td>
                        <td><x-badge :tone="$meal->in_stock ? 'success' : 'danger'">{{ $meal->max_portions }} {{ __('app.portions') }}</x-badge></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('menu.show', $meal) }}"><x-icon name="eye" /></a>
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.meals.edit', $meal) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.meals.destroy', $meal)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <x-empty-state :title="__('app.no_meals')" :description="__('app.no_meals_hint')">
                            <a class="btn" href="{{ route('manage.meals.create') }}">{{ __('app.new_meal') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $meals->links() }}
    </x-card>
@endsection
