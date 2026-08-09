@extends('layouts.manage')

@section('title', __('app.edit_table'))
@section('eyebrow', __('app.tables'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.tables.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    <x-delete-form :action="route('manage.tables.destroy', $table)" :label="__('app.delete')" />
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.tables.update', $table) }}">
            @csrf
            @method('PUT')
            @include('manage.tables._form')
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save_changes') }}</button>
        </form>
    </x-card>
@endsection
