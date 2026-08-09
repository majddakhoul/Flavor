@extends('layouts.account')

@section('title', __('app.profile'))

@section('content')
    <x-card :title="__('app.personal_details')">
        <form method="POST" action="{{ route('account.profile.update') }}">
            @csrf @method('PUT')
            <div class="grid grid-2">
                <x-field name="first_name" :label="__('app.first_name')" required><x-input name="first_name" :value="$user->first_name" /></x-field>
                <x-field name="last_name" :label="__('app.last_name')" required><x-input name="last_name" :value="$user->last_name" /></x-field>
            </div>
            <div class="grid grid-2">
                <x-field name="phone" :label="__('app.phone')" required><x-input name="phone" :value="$user->phone" dir="ltr" /></x-field>
                <x-field name="gender" :label="__('app.gender')" required>
                    <x-select name="gender" :options="$genders" :selected="$user->gender?->value" />
                </x-field>
            </div>
            <x-field name="location_id" :label="__('app.delivery_area')">
                <x-select name="location_id" :options="$locations" :selected="$user->location_id" :placeholder="__('app.choose')" />
            </x-field>
            <div class="grid grid-2">
                <x-field name="allergies" :label="__('app.allergies')" :hint="__('app.allergies_hint')">
                    <x-select name="allergies" :options="$allergies" :selected="$user->customer?->allergies?->value" :placeholder="__('app.none')" />
                </x-field>
                <x-field name="favorite_categories" :label="__('app.favorite_categories')">
                    <x-input name="favorite_categories" :value="$user->customer?->favorite_categories" />
                </x-field>
            </div>
            <button class="btn" type="submit">{{ __('app.save_changes') }}</button>
        </form>
    </x-card>

    <x-card :title="__('app.password')">
        <form method="POST" action="{{ route('account.profile.password') }}">
            @csrf @method('PUT')
            <x-field name="current_password" :label="__('app.current_password')" required><x-input name="current_password" type="password" /></x-field>
            <div class="grid grid-2">
                <x-field name="password" :label="__('app.new_password')" required><x-input name="password" type="password" /></x-field>
                <x-field name="password_confirmation" :label="__('app.confirm_password')" required><x-input name="password_confirmation" type="password" /></x-field>
            </div>
            <button class="btn" type="submit">{{ __('app.update_password') }}</button>
        </form>
    </x-card>

    <x-card :title="__('app.danger_zone')">
        <p class="small muted">{{ __('app.deactivate_hint') }}</p>
        <x-delete-form :action="route('account.profile.deactivate')" :label="__('app.deactivate_account')" :confirm="__('app.confirm_deactivate')" />
    </x-card>
@endsection
