@extends('layouts.manage')

@section('title', __('app.edit_ingredient'))
@section('eyebrow', __('app.ingredients'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.ingredients.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.ingredients.destroy', $ingredient)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.ingredients.update', $ingredient) }}">
            @csrf
            @method('PUT')
            @include('manage.ingredients._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
