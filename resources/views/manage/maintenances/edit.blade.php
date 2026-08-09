@extends('layouts.manage')

@section('title', __('app.edit_maintenance'))
@section('eyebrow', __('app.maintenances'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.maintenances.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.maintenances.destroy', $maintenance)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.maintenances.update', $maintenance) }}">
            @csrf
            @method('PUT')
            @include('manage.maintenances._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
