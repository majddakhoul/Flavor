@extends('layouts.site')

@section('title', __('app.menu_nav'))

@section('content')
    <section class="shell" style="padding-block:var(--space-4)">
        <p class="eyebrow">{{ __('app.tagline_short') }}</p>
        <h1>{{ __('app.menu_nav') }}</h1>
        <p class="lede">{{ __('app.menu_intro') }}</p>

        <x-toolbar :action="route('menu.index')" :sorts="['name' => __('app.name'), 'created_at' => __('app.newest')]">
            <x-filter-select name="category_id" :label="__('app.category')" :options="$categories->pluck('name', 'id')->all()" />
            <x-filter-select name="vegetarian" :label="__('app.diet')" :options="['1' => __('app.vegetarian')]" />
        </x-toolbar>

        <div class="grid grid-3">
            @forelse ($meals as $meal)
                <x-dish-card :meal="$meal" />
            @empty
                <x-empty-state :title="__('app.no_results')" :description="__('app.no_results_hint')" />
            @endforelse
        </div>

        {{ $meals->links() }}
    </section>
@endsection
