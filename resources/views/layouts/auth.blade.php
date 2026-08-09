@extends('layouts.base')

@section('body')
    <main id="main" class="shell" style="min-height:100vh;display:grid;place-items:center;padding:var(--space-5) 0">
        <div style="width:min(460px,100%)">
            <div class="center" style="margin-bottom:var(--space-4)">
                <a class="brand" href="{{ route('home') }}" style="justify-content:center">
                    <img src="{{ asset('assets/img/brand/logo-192.png') }}" alt="">
                    <span>{{ config('flavor.brand.name') }}<small>{{ __('app.tagline_short') }}</small></span>
                </a>
            </div>

            <div class="card">
                <h1 style="font-size:var(--step-2)">@yield('heading')</h1>
                <p class="muted small">@yield('subheading')</p>
                @yield('form')
            </div>

            <div class="cluster center" style="justify-content:center;margin-top:var(--space-3)">
                <x-locale-switcher />
                <x-theme-toggle />
            </div>
        </div>
    </main>
@endsection
