<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="crm-page-title">Notifications</h1>
                <span class="crm-page-subtitle">Follow-up reminders and system alerts.</span>
            </div>
            @if(auth()->user()->unreadNotifications->isNotEmpty())
                <div class="crm-header-actions mt-2 mt-md-0">
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn btn-default btn-sm">
                            <i class="fas fa-check-double"></i> Mark all as read
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                    @endphp
                    <li class="list-group-item {{ $isUnread ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    @if($isUnread)
                                        <span class="badge badge-warning mr-2">New</span>
                                    @endif
                                    <strong>{{ $data['message'] ?? 'Notification' }}</strong>
                                </div>
                                @if(! empty($data['follow_up_date']))
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-calendar"></i>
                                        Follow-up date: {{ \Carbon\Carbon::parse($data['follow_up_date'])->format('M j, Y') }}
                                        @if(! empty($data['is_overdue']))
                                            <span class="badge badge-danger ml-1">Overdue</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="small text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="ml-3">
                                @if(! empty($data['url']))
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Lead
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">
                        No notifications yet.
                    </li>
                @endforelse
            </ul>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
