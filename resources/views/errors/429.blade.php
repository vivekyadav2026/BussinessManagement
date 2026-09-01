@extends('errors.minimal')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Rate Limit Exceeded')
@section('message', 'You have made too many requests in a short period of time. Please wait a few seconds and refresh.')

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
        Reload Page
    </button>
@endsection
