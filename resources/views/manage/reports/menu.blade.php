@extends('layouts.manage')

@section('title', __('app.menu_report'))
@section('eyebrow', __('app.section_reports'))

@section('content')
    <div class="split">
        <x-card :title="__('app.top_selling')">
            <ol class="rank">
                @forelse ($performance['top_meals'] as $index => $meal)
                    <li>
                        <span class="rank__no">{{ $index + 1 }}</span>
                        <span class="rank__body">
                            <b>{{ $meal->name }}</b>
                            <div class="progress"><span style="width:{{ $performance['top_meals']->max('sold') > 0 ? round($meal->sold / $performance['top_meals']->max('sold') * 100) : 0 }}%"></span></div>
                        </span>
                        <span class="mono small">{{ $meal->sold }}</span>
                    </li>
                @empty
                    <p class="muted small">{{ __('app.no_sales_yet') }}</p>
                @endforelse
            </ol>
        </x-card>

        <x-card :title="__('app.menu_mix')">
            <div class="stack">
                @foreach ($performance['menu_mix'] as $category => $count)
                    <div class="between">
                        <span>{{ $category }}</span>
                        <b class="mono">{{ $count }}</b>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    <x-card :title="__('app.unavailable_dishes')">
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr><th>{{ __('app.meal') }}</th><th>{{ __('app.category') }}</th><th style="text-align:end">{{ __('app.actions') }}</th></tr>
                </thead>
                <tbody>
                @forelse ($performance['unavailable'] as $meal)
                    <tr>
                        <td>{{ $meal->t('name') }}</td>
                        <td>{{ $meal->category?->t('name') }}</td>
                        <td><div class="table__actions">
                            <a class="btn btn--ghost btn--sm" href="{{ route('manage.meals.edit', $meal) }}"><x-icon name="edit" />{{ __('app.fix') }}</a>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="3"><x-empty-state :title="__('app.everything_available')" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
