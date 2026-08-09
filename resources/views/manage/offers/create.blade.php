@extends('layouts.manage')

@section('title', __('app.new_offer'))
@section('eyebrow', __('app.offers'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.offers.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.offers.store') }}">
            @csrf
            @include('manage.offers._form', ['offer' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
