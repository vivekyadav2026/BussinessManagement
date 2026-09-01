@extends('errors.minimal')

@section('title', 'Server Error')
@section('code', '500')
@section('heading', 'Internal Server Error')
@section('message', 'An unexpected error occurred on our server while processing your request. Our technical team has been notified.')

@section('actions')
    @auth
        <a href="{{ route('organization.dashboard') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Return to Dashboard
        </a>
    @else
        <a href="/" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Go Home
        </a>
    @endauth
    <button onclick="window.location.reload()" class="w-full sm:w-auto bg-slate-700 hover:bg-slate-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
        Reload Page
    </button>
@endsection
