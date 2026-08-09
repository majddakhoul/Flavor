@extends('layouts.manage')

@section('title', __('app.categories'))
@section('eyebrow', __('app.section_kitchen'))

@section('actions')
    <a class="btn" href="{{ route('manage.categories.create') }}"><x-icon name="plus" />{{ __('app.new_category') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.categories.index')" :sorts="['name' => __('app.name'), 'created_at' => __('app.newest')]">
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.name') }}</th>
                    <th>{{ __('app.parent') }}</th>
                    <th>{{ __('app.dishes') }}</th>
                    <th>{{ __('app.description') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->t('name') }}</td>
                        <td>{{ $category->parent?->t('name') ?? '—' }}</td>
                        <td><span class="mono">{{ $category->meals->count() }}</span></td>
                        <td><span class="small muted">{{ $category->t('description') }}</span></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.categories.edit', $category) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.categories.destroy', $category)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <x-empty-state illustration="empty-box" :title="__('app.no_categories')" :description="__('app.no_categories_hint')">
                            <a class="btn" href="{{ route('manage.categories.create') }}">{{ __('app.new_category') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}
    </x-card>
@endsection
