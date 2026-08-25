@extends('layouts.sme')

@section('content')
<div class="p-4 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Notifications</h1>
        @if($notifications->count() > 0)
        <form action="{{ route('organization.notifications.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="text-blue-600 hover:underline">Mark all as read</button>
        </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        @forelse($notifications as $notification)
            <div class="p-4 border-b flex gap-4 {{ $notification->read_at ? 'opacity-75 bg-gray-50' : 'bg-white' }}">
                <div class="mt-1">
                    @if($notification->data['type'] ?? '' == 'complaint_assigned')
                        <span class="text-blue-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></span>
                    @elseif($notification->data['type'] ?? '' == 'low_stock')
                        <span class="text-red-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></span>
                    @else
                        <span class="text-gray-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-800 {{ !$notification->read_at ? 'text-black' : '' }}">
                        {{ $notification->data['subject'] ?? 'Notification' }}
                    </p>
                    <p class="text-gray-600 text-sm mt-1">
                        {{ $notification->data['message'] ?? 'You have a new alert.' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                <div>
                    @if(!$notification->read_at)
                    <form action="{{ route('organization.notifications.markAsRead', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-3 h-3 bg-blue-600 rounded-full" title="Mark as read"></button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                You have no notifications.
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
