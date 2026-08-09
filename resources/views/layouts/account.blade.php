@extends('layouts.base')

@section('body')
    @include('partials.header')

    <main id="main" class="shell" style="padding-block:var(--space-4) var(--space-6)">
        <div class="page-head" style="margin-bottom:var(--space-4)">
            <div>
                <p class="eyebrow">@yield('eyebrow', __('app.my_account'))</p>
                <h1>@yield('title')</h1>
            </div>
            <div class="cluster">@yield('actions')</div>
        </div>

        <div class="split">
            <div class="stack">@yield('content')</div>
            <aside class="stack">@include('partials.account-nav')</aside>
        </div>
    </main>

    @include('partials.footer')
@endsection
