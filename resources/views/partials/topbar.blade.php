@php($user = auth()->user())
<header class="topbar">
    <button class="btn btn--ghost btn--icon" data-toggle-target="#workspace-sidebar" aria-expanded="false" aria-label="{{ __('app.menu') }}" style="display:none" id="sidebar-trigger">
        <x-icon name="menu" />
    </button>

    <div class="topbar__title">
        {{ __('app.greeting', ['name' => $user->first_name]) }}
        <small>{{ now()->translatedFormat('l, d F Y') }}</small>
    </div>

    <div class="topbar__tools">
        <x-locale-switcher />
        <x-theme-toggle />

        <div class="menu-pop">
            <button type="button" class="btn btn--ghost btn--sm" data-toggle-target="#workspace-user" aria-expanded="false">
                <span class="avatar">{{ $user->initials }}</span>
                <span class="small">{{ $user->employee?->position?->label() ?? $user->user_type->label() }}</span>
            </button>
            <div class="menu-pop__panel" id="workspace-user">
                <a href="{{ route('home') }}"><x-icon name="globe" />{{ __('app.view_site') }}</a>
                <hr>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><x-icon name="logout" />{{ __('app.sign_out') }}</button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    (function () {
        const trigger = document.getElementById('sidebar-trigger');
        const apply = () => { trigger.style.display = window.innerWidth <= 1000 ? 'inline-flex' : 'none'; };
        apply();
        window.addEventListener('resize', apply);
    })();
</script>
@endpush
