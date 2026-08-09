@extends('layouts.auth')

@section('title', __('app.create_account'))
@section('heading', __('app.join_flavor'))
@section('subheading', __('app.register_subtitle'))

@section('form')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="grid grid-2">
            <x-field name="first_name" :label="__('app.first_name')" required><x-input name="first_name" autofocus /></x-field>
            <x-field name="last_name" :label="__('app.last_name')" required><x-input name="last_name" /></x-field>
        </div>

        <x-field name="email" :label="__('app.email')" required><x-input name="email" type="email" /></x-field>
        <x-field name="phone" :label="__('app.phone')" :hint="__('app.phone_hint')" required><x-input name="phone" dir="ltr" /></x-field>

        <div class="grid grid-2">
            <x-field name="gender" :label="__('app.gender')" required>
                <x-select name="gender" :options="\App\Enums\Gender::options()" :placeholder="__('app.choose')" />
            </x-field>
            <x-field name="location_id" :label="__('app.delivery_area')">
                <x-select name="location_id" :options="$locations" :placeholder="__('app.choose')" />
            </x-field>
        </div>

        <x-field name="password" :label="__('app.password')" required><x-input name="password" type="password" autocomplete="new-password" /></x-field>
        <x-field name="password_confirmation" :label="__('app.confirm_password')" required><x-input name="password_confirmation" type="password" autocomplete="new-password" /></x-field>

        <button class="btn btn--block" type="submit">{{ __('app.create_account') }}</button>
    </form>

    <p class="small center muted" style="margin-top:var(--space-3)">
        {{ __('app.have_account') }} <a href="{{ route('login') }}">{{ __('app.sign_in') }}</a>
    </p>
@endsection
