@extends('errors.minimal')

@section('title', 'Page Expired')
@section('code', '419')
@section('heading', 'Session Expired')
@section('message', 'Your security token or form session has expired due to inactivity. Please refresh the page and submit again.')

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
        Refresh & Try Again
    </button>
    <a href="{{ route('login') }}" class="w-full sm:w-auto bg-slate-700 hover:bg-slate-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
        Sign In Again
    </a>
@endsection
