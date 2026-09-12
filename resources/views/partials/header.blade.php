<header class="site-header">
    <div class="shell site-header__inner">
        <a class="brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/brand/mark.svg') }}" alt="">
            <span>{{ config('flavor.brand.name') }}<small>{{ __('app.tagline_short') }}</small></span>
        </a>

        <button class="btn btn--ghost btn--icon nav-toggle" data-toggle-target="#site-nav" aria-expanded="false" aria-label="{{ __('app.menu') }}">
            <x-icon name="menu" />
        </button>

        <nav class="nav" id="site-nav" aria-label="{{ __('app.primary_navigation') }}">
            <a href="{{ route('menu.index') }}" @class(['is-active' => request()->routeIs('menu.*')])>{{ __('app.menu_nav') }}</a>
            <a href="{{ route('offers.index') }}" @class(['is-active' => request()->routeIs('offers.*')])>{{ __('app.offers') }}</a>

            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('account.reservations.create') }}">{{ __('app.book_table') }}</a>
                    <a href="{{ route('account.cart.index') }}" @class(['is-active' => request()->routeIs('account.cart.*')])>
                        {{ __('app.cart') }}@if (($cartCount ?? 0) > 0) <x-badge tone="brand" plain>{{ $cartCount }}</x-badge>@endif
                    </a>
                @endif
                @if (auth()->user()->isStaff())
                    <a href="{{ route('manage.dashboard') }}">{{ __('app.workspace') }}</a>
                @endif
            @endauth

            <x-locale-switcher />
            <x-theme-toggle />
            <x-notification-bell />

            @auth
                <div class="menu-pop">
                    <button type="button" class="btn btn--ghost btn--sm" data-toggle-target="#user-menu" aria-expanded="false">
                        <span class="avatar">{{ auth()->user()->initials }}</span>
                        <span>{{ auth()->user()->first_name }}</span>
                    </button>
                    <div class="menu-pop__panel" id="user-menu">
                        @if (auth()->user()->isCustomer())
                            <a href="{{ route('account.dashboard') }}"><x-icon name="dashboard" />{{ __('app.my_account') }}</a>
                            <a href="{{ route('account.orders.index') }}"><x-icon name="orders" />{{ __('app.my_orders') }}</a>
                            <a href="{{ route('account.reservations.index') }}"><x-icon name="calendar" />{{ __('app.my_reservations') }}</a>
                            <a href="{{ route('account.profile.edit') }}"><x-icon name="user" />{{ __('app.profile') }}</a>
                        @endif
                        <hr>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"><x-icon name="logout" />{{ __('app.sign_out') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="btn btn--ghost btn--sm" href="{{ route('login') }}">{{ __('app.sign_in') }}</a>
                <a class="btn btn--sm" href="{{ route('register') }}">{{ __('app.create_account') }}</a>
            @endauth
        </nav>
    </div>
</header>
