@extends('layouts.auth')

@section('title', __('app.reset_password'))
@section('heading', __('app.reset_password'))
@section('subheading', __('app.reset_subtitle'))

@section('form')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" :label="__('app.email')" required><x-input name="email" type="email" :value="request('email')" /></x-field>
        <x-field name="password" :label="__('app.new_password')" required><x-input name="password" type="password" /></x-field>
        <x-field name="password_confirmation" :label="__('app.confirm_password')" required><x-input name="password_confirmation" type="password" /></x-field>
        <button class="btn btn--block" type="submit">{{ __('app.reset_password') }}</button>
    </form>
@endsection
