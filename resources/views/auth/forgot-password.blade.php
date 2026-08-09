@extends('layouts.auth')

@section('title', __('app.forgot_password'))
@section('heading', __('app.forgot_password'))
@section('subheading', __('app.forgot_subtitle'))

@section('form')
    @if (session('status'))<p class="badge badge--success">{{ session('status') }}</p>@endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-field name="email" :label="__('app.email')" required><x-input name="email" type="email" autofocus /></x-field>
        <button class="btn btn--block" type="submit">{{ __('app.send_reset_link') }}</button>
    </form>

    <p class="small center muted" style="margin-top:var(--space-3)">
        <a href="{{ route('login') }}">{{ __('app.back_to_sign_in') }}</a>
    </p>
@endsection
