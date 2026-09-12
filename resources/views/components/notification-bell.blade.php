@auth
    @php($notifications = app(\App\Services\Support\NotificationService::class))
    @php($recent = $notifications->recent(auth()->user()))
    @php($unread = $notifications->unreadCount(auth()->user()))
    <div class="menu-pop">
        <button type="button" class="btn btn--ghost btn--icon notif-trigger" data-toggle-target="#notif-panel" aria-expanded="false" aria-label="{{ __('app.notifications') }}">
            <x-icon name="bell" />
            @if ($unread > 0)
                <span class="notif-dot">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </button>
        <div class="menu-pop__panel notif-panel" id="notif-panel">
            <div class="notif-panel__head">
                <b>{{ __('app.notifications') }}</b>
                @if ($unread > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="small">{{ __('app.mark_all_read') }}</button>
                    </form>
                @endif
            </div>

            @forelse ($recent as $item)
                <a href="{{ route('notifications.read', $item->id) }}" class="notif-item @unless($item->read_at) notif-item--unread @endunless">
                    <span class="notif-icon notif-icon--{{ $item->data['tone'] ?? 'info' }}">
                        <x-icon :name="$item->data['icon'] ?? 'bell'" />
                    </span>
                    <span>
                        <b>{{ $item->data['title'] ?? '' }}</b>
                        <p class="small muted" style="margin:.1rem 0 0">{{ $item->created_at->diffForHumans() }}</p>
                    </span>
                </a>
            @empty
                <p class="small muted" style="padding:.75rem">{{ __('app.no_notifications') }}</p>
            @endforelse

            <a href="{{ route('notifications.index') }}" class="notif-panel__all">{{ __('app.view_all_notifications') }}</a>
        </div>
    </div>
@endauth
