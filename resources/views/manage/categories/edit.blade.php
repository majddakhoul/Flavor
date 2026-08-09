@extends('layouts.manage')

@section('title', __('app.edit_category'))
@section('eyebrow', __('app.categories'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.categories.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.categories.destroy', $category)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('manage.categories._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
