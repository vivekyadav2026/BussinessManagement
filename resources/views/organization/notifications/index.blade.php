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

    <div class="bg-white rounded-xl shadow-2xs border border-slate-200/90 overflow-hidden">
        @forelse($notifications as $notification)
            <div class="p-4 border-b border-slate-100 flex gap-4 {{ $notification->read_at ? 'opacity-75 bg-slate-50/50' : 'bg-white' }} hover:bg-slate-50 transition">
                <div class="mt-1">
                    @if($notification->data['type'] ?? '' == 'complaint_assigned')
                        <span class="text-blue-500 bg-blue-50 p-2 rounded-lg inline-block"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></span>
                    @elseif($notification->data['type'] ?? '' == 'low_stock')
                        <span class="text-red-500 bg-red-50 p-2 rounded-lg inline-block"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></span>
                    @else
                        <span class="text-slate-500 bg-slate-100 p-2 rounded-lg inline-block"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-extrabold text-sm {{ !$notification->read_at ? 'text-slate-950' : 'text-slate-800' }}">
                                {{ $notification->data['subject'] ?? 'Notification' }}
                            </p>
                            <p class="text-slate-600 text-xs font-medium mt-1">
                                {{ $notification->data['message'] ?? 'You have a new alert.' }}
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-wider">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="shrink-0 ml-4">
                            @if(!$notification->read_at)
                            <form action="{{ route('organization.notifications.markAsRead', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-extrabold rounded-md transition border border-emerald-200 uppercase tracking-wider" title="Mark as read">
                                    ✓ Mark Read
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center mb-4">
                    <span class="text-2xl">📭</span>
                </div>
                <h3 class="text-slate-900 font-extrabold text-sm">No Notifications</h3>
                <p class="text-slate-500 text-xs mt-1">You're all caught up! There are no new alerts.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
