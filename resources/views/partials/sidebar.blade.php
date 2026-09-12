@php($user = auth()->user())
<aside class="sidebar" id="workspace-sidebar">
    <a class="sidebar__brand" href="{{ route('manage.dashboard') }}">
        <img src="{{ asset('assets/img/brand/mark.svg') }}" alt="">
        <span><b>{{ config('flavor.brand.name') }}</b><small>{{ __('app.workspace') }}</small></span>
    </a>

    <div class="sidebar__group">
        <a href="{{ route('manage.dashboard') }}" @class(['is-active' => request()->routeIs('manage.dashboard')])>
            <x-icon name="dashboard" />{{ __('app.dashboard') }}
        </a>
    </div>

    @ability('sales')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_sales') }}</span>
            <a href="{{ route('manage.orders.index') }}" @class(['is-active' => request()->routeIs('manage.orders.index')])><x-icon name="orders" />{{ __('app.orders') }}</a>
            <a href="{{ route('manage.orders.trashed') }}" @class(['is-active' => request()->routeIs('manage.orders.trashed')])><x-icon name="trash" />{{ __('app.cancelled_orders') }}</a>
        </div>
    @endability

    @ability('floor')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_floor') }}</span>
            <a href="{{ route('manage.reservations.index') }}" @class(['is-active' => request()->routeIs('manage.reservations.*')])><x-icon name="calendar" />{{ __('app.reservations') }}</a>
            <a href="{{ route('manage.tables.index') }}" @class(['is-active' => request()->routeIs('manage.tables.*')])><x-icon name="table" />{{ __('app.tables') }}</a>
        </div>
    @endability

    @ability('catalog')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_kitchen') }}</span>
            <a href="{{ route('manage.meals.index') }}" @class(['is-active' => request()->routeIs('manage.meals.*')])><x-icon name="chef" />{{ __('app.meals') }}</a>
            <a href="{{ route('manage.categories.index') }}" @class(['is-active' => request()->routeIs('manage.categories.*')])><x-icon name="menu-book" />{{ __('app.categories') }}</a>
            <a href="{{ route('manage.offers.index') }}" @class(['is-active' => request()->routeIs('manage.offers.*')])><x-icon name="tag" />{{ __('app.offers') }}</a>
        </div>
    @endability

    @ability('inventory')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_inventory') }}</span>
            <a href="{{ route('manage.ingredients.index') }}" @class(['is-active' => request()->routeIs('manage.ingredients.*')])><x-icon name="box" />{{ __('app.ingredients') }}</a>
            <a href="{{ route('manage.maintenances.index') }}" @class(['is-active' => request()->routeIs('manage.maintenances.*')])><x-icon name="tools" />{{ __('app.maintenance') }}</a>
        </div>
    @endability

    @ability('people')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_people') }}</span>
            <a href="{{ route('manage.employees.index') }}" @class(['is-active' => request()->routeIs('manage.employees.*')])><x-icon name="users" />{{ __('app.employees') }}</a>
            <a href="{{ route('manage.customers.index') }}" @class(['is-active' => request()->routeIs('manage.customers.*')])><x-icon name="user" />{{ __('app.customers') }}</a>
            <a href="{{ route('manage.locations.index') }}" @class(['is-active' => request()->routeIs('manage.locations.*')])><x-icon name="pin" />{{ __('app.locations') }}</a>
        </div>
    @endability

    @ability('reports')
        <div class="sidebar__group">
            <span class="sidebar__label">{{ __('app.section_reports') }}</span>
            <a href="{{ route('manage.reports.finance') }}" @class(['is-active' => request()->routeIs('manage.reports.finance')])><x-icon name="money" />{{ __('app.finance') }}</a>
            <a href="{{ route('manage.reports.inventory') }}" @class(['is-active' => request()->routeIs('manage.reports.inventory')])><x-icon name="chart" />{{ __('app.stock_report') }}</a>
            <a href="{{ route('manage.reports.menu') }}" @class(['is-active' => request()->routeIs('manage.reports.menu')])><x-icon name="fire" />{{ __('app.menu_report') }}</a>
        </div>
    @endability

    <div class="sidebar__foot">
        <a href="{{ route('home') }}"><x-icon name="globe" />{{ __('app.view_site') }}</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn--ghost btn--block btn--sm" style="margin-top:.4rem"><x-icon name="logout" />{{ __('app.sign_out') }}</button>
        </form>
    </div>
</aside>
