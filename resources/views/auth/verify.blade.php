@extends('layouts.auth')

@section('title', __('app.verify_email'))
@section('heading', __('app.verify_email'))
@section('subheading', __('app.verify_subtitle', ['email' => auth()->user()->email]))

@section('form')
    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf
        <x-field name="code" :label="__('app.verification_code')" :hint="__('app.verification_hint')" required>
            <input class="input mono" id="code" name="code" inputmode="numeric" maxlength="6" dir="ltr"
                   style="letter-spacing:.5em;text-align:center;font-size:1.3rem" autofocus>
        </x-field>
        <button class="btn btn--block" type="submit">{{ __('app.verify') }}</button>
    </form>

    <form method="POST" action="{{ route('verification.resend') }}" style="margin-top:var(--space-3)">
        @csrf
        <button class="btn btn--ghost btn--block" type="submit"><x-icon name="refresh" />{{ __('app.resend_code') }}</button>
    </form>
@endsection
