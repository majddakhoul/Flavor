@extends('layouts.manage')

@section('title', __('app.new_employee'))
@section('eyebrow', __('app.employees'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.employees.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.employees.store') }}">
            @csrf
            @include('manage.employees._form', ['employee' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
