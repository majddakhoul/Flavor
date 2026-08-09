@extends('layouts.auth')

@section('title', __('app.sign_in'))
@section('heading', __('app.welcome_back'))
@section('subheading', __('app.sign_in_subtitle'))

@section('form')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <x-field name="email" :label="__('app.email')" required>
            <x-input name="email" type="email" autocomplete="email" autofocus />
        </x-field>

        <x-field name="password" :label="__('app.password')" required>
            <x-input name="password" type="password" autocomplete="current-password" />
        </x-field>

        <div class="between" style="margin-bottom:var(--space-3)">
            <label class="check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>{{ __('app.remember_me') }}</span>
            </label>
            <a class="small" href="{{ route('password.request') }}">{{ __('app.forgot_password') }}</a>
        </div>

        <button class="btn btn--block" type="submit">{{ __('app.sign_in') }}</button>
    </form>

    <p class="small center muted" style="margin-top:var(--space-3)">
        {{ __('app.no_account') }} <a href="{{ route('register') }}">{{ __('app.create_account') }}</a>
    </p>
@endsection
