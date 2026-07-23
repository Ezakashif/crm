@auth
    @if (auth()->user()->hasPermission('view.notifications'))
        @php
            $unreadCount = auth()->user()->unreadNotifications()->count();
            $latestNotifications = auth()->user()->notifications()->latest()->limit(5)->get();
        @endphp
        <details class="crm-notification-dropdown">
            <summary aria-label="Notifications">
                <i class="far fa-bell" aria-hidden="true"></i>
                @if ($unreadCount)
                    <span class="crm-notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </summary>
            <div class="crm-notification-popover">
                <div class="crm-notification-popover__header">
                    <strong>Notifications</strong>
                    @if ($unreadCount)
                        <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button type="submit">Mark all read</button></form>
                    @endif
                </div>
                @forelse ($latestNotifications as $notification)
                    @php($data = $notification->data)
                    <a class="crm-notification-preview {{ $notification->read_at ? '' : 'is-unread' }}" href="{{ route('notifications.index') }}">
                        <strong>{{ $data['subject'] ?? 'Notification' }}</strong>
                        <span>{{ $data['message'] ?? 'You have a new notification.' }}</span>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </a>
                @empty
                    <p class="crm-notification-empty">No notifications yet.</p>
                @endforelse
                <a class="crm-notification-popover__footer" href="{{ route('notifications.index') }}">View all notifications</a>
            </div>
        </details>
    @endif
@endauth
