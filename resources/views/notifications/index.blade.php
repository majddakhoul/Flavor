@extends(auth()->user()->isStaff() ? 'layouts.manage' : 'layouts.account')

@section('title', __('app.notifications'))
@section('eyebrow', __('app.notifications'))

@section('actions')
    @if ($notifications->total() > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="btn btn--ghost btn--sm" type="submit"><x-icon name="check" />{{ __('app.mark_all_read') }}</button>
        </form>
    @endif
@endsection

@section('content')
    <x-card :flush="false">
        @forelse ($notifications as $notification)
            <div class="between notif-row @unless($notification->read_at) notif-row--unread @endunless">
                <a class="cluster" style="gap:.75rem;text-decoration:none;color:inherit" href="{{ route('notifications.read', $notification->id) }}">
                    <span class="notif-icon notif-icon--{{ $notification->data['tone'] ?? 'info' }}">
                        <x-icon :name="$notification->data['icon'] ?? 'bell'" />
                    </span>
                    <span>
                        <b>{{ $notification->data['title'] ?? '' }}</b>
                        <p class="small muted" style="margin:.15rem 0 0">{{ $notification->data['body'] ?? '' }}</p>
                        <p class="small muted" style="margin:.15rem 0 0">{{ $notification->created_at->diffForHumans() }}</p>
                    </span>
                </a>
                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn--ghost btn--icon btn--sm" type="submit" aria-label="{{ __('app.delete') }}">
                        <x-icon name="trash" />
                    </button>
                </form>
            </div>
        @empty
            <x-empty-state :title="__('app.no_notifications')" :description="__('app.no_notifications_hint')" />
        @endforelse

        {{ $notifications->links() }}
    </x-card>
@endsection
