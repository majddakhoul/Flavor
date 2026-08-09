@extends('layouts.manage')

@section('title', __('app.new_ingredient'))
@section('eyebrow', __('app.ingredients'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.ingredients.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.ingredients.store') }}">
            @csrf
            @include('manage.ingredients._form', ['ingredient' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
