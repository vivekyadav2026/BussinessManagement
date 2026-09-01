@extends('errors.minimal')

@section('title', 'Page Not Found')
@section('code', '404')
@section('heading', 'Page Not Found')
@section('message', 'The page or resource you are looking for might have been moved, deleted, or is temporarily unavailable.')

@section('actions')
    @auth
        <a href="{{ route('organization.dashboard') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Back to Dashboard
        </a>
    @else
        <a href="/" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Go to Homepage
        </a>
    @endauth
@endsection
