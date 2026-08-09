@extends('layouts.manage')

@section('title', __('app.new_table'))
@section('eyebrow', __('app.tables'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.tables.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.tables.store') }}">
            @csrf
            @include('manage.tables._form', ['table' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
