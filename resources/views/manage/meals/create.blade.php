@extends('layouts.manage')

@section('title', __('app.new_meal'))
@section('eyebrow', __('app.meals'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.meals.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card>
        <form method="POST" action="{{ route('manage.meals.store') }}" enctype="multipart/form-data">
            @csrf
            @include('manage.meals._form', ['meal' => null])
            <button class="btn" type="submit"><x-icon name="check" />{{ __('app.save') }}</button>
        </form>
        <p class="field__hint" style="margin-top:var(--space-2)">{{ __('app.recipe_after_save') }}</p>
    </x-card>
@endsection
