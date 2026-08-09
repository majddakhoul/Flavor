@extends('layouts.base')

@section('body')
    <main id="main" class="shell center" style="min-height:100vh;display:grid;place-content:center;gap:var(--space-3)">
        <img src="{{ asset('assets/img/brand/logo-192.png') }}" alt="" style="margin-inline:auto;width:72px">
        <p class="eyebrow">{{ __('errors.code', ['code' => '419']) }}</p>
        <h1>{{ __('errors.title_419') }}</h1>
        <p class="lede" style="margin-inline:auto">{{ ($exception ?? null)?->getMessage() ?: __('errors.body_419') }}</p>
        <div class="cluster" style="justify-content:center">
            <a class="btn" href="{{ route('home') }}"><x-icon name="back" />{{ __('app.back_home') }}</a>
        </div>
    </main>
@endsection
