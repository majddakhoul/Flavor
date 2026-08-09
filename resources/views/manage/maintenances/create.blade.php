@extends('layouts.manage')

@section('title', __('app.new_maintenance'))
@section('eyebrow', __('app.maintenances'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.maintenances.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.maintenances.store') }}">
            @csrf
            @include('manage.maintenances._form', ['maintenance' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
