@extends('layouts.manage')

@section('title', __('app.new_category'))
@section('eyebrow', __('app.categories'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.categories.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.categories.store') }}">
            @csrf
            @include('manage.categories._form', ['category' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
    </x-card>
@endsection
