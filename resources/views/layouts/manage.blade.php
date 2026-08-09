@extends('layouts.base')

@section('body')
    <div class="app">
        @include('partials.sidebar')

        <div class="workspace">
            @include('partials.topbar')

            <main id="main" class="workspace__body">
                <div class="page-head">
                    <div>
                        <p class="eyebrow">@yield('eyebrow', __('app.workspace'))</p>
                        <h1>@yield('title')</h1>
                    </div>
                    <div class="cluster">@yield('actions')</div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>
@endsection
