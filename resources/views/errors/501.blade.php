@extends('errors.minimal')

@section('title', 'Feature Not Implemented')
@section('code', '501')
@section('heading', 'Not Implemented')
@section('message', 'This feature or server protocol is currently not implemented or supported in this environment.')

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
@endsection
