<x-card :title="__('app.my_account')">
    <div class="stack">
        <a class="btn btn--ghost btn--block" href="{{ route('account.dashboard') }}"><x-icon name="dashboard" />{{ __('app.overview') }}</a>
        <a class="btn btn--ghost btn--block" href="{{ route('account.orders.index') }}"><x-icon name="orders" />{{ __('app.my_orders') }}</a>
        <a class="btn btn--ghost btn--block" href="{{ route('account.reservations.index') }}"><x-icon name="calendar" />{{ __('app.my_reservations') }}</a>
        <a class="btn btn--ghost btn--block" href="{{ route('account.cart.index') }}"><x-icon name="cart" />{{ __('app.cart') }}</a>
        <a class="btn btn--ghost btn--block" href="{{ route('account.profile.edit') }}"><x-icon name="user" />{{ __('app.profile') }}</a>
    </div>
</x-card>

<x-card :title="__('app.need_a_table')">
    <p class="small muted">{{ __('app.reservation_blurb') }}</p>
    <a class="btn btn--block" href="{{ route('account.reservations.create') }}"><x-icon name="calendar" />{{ __('app.book_table') }}</a>
</x-card>
