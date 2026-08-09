@extends('layouts.manage')

@section('title', __('app.edit_employee'))
@section('eyebrow', __('app.employees'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.employees.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.employees.destroy', $employee)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.employees.update', $employee) }}">
            @csrf
            @method('PUT')
            @include('manage.employees._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
