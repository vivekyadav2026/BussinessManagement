@extends('errors.minimal')

@section('title', 'Access Denied')
@section('code', '403')
@section('heading', 'Access Restricted')
@section('message', $exception->getMessage() ?: 'You do not have permission or your active subscription plan feature does not permit access to this module.')

@section('actions')
    @auth
        @if(auth()->user()->hasRole('Super Admin'))
            <a href="{{ route('super-admin.dashboard') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
                Super Admin Dashboard
            </a>
        @else
            <a href="{{ route('organization.dashboard') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
                Return to Dashboard
            </a>
            <a href="{{ route('organization.subscription.index') }}" class="w-full sm:w-auto bg-slate-700 hover:bg-slate-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
                View Subscription Plans
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Sign In
        </a>
    @endauth
@endsection
