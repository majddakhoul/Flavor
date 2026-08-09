@extends('layouts.manage')

@section('title', __('app.new_location'))
@section('eyebrow', __('app.locations'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.locations.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.locations.store') }}">
            @csrf
            @include('manage.locations._form', ['location' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
