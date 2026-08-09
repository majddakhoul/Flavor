@extends('layouts.manage')

@section('title', __('app.edit_location'))
@section('eyebrow', __('app.locations'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.locations.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.locations.destroy', $location)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.locations.update', $location) }}">
            @csrf
            @method('PUT')
            @include('manage.locations._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
