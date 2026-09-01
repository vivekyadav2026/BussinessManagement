@extends('errors.minimal')

@section('title', 'Payment Required')
@section('code', '402')
@section('heading', 'Payment or Plan Upgrade Required')
@section('message', 'Your current subscription plan does not include access to this feature or your free trial has ended. Please upgrade your plan to unlock access.')

@section('actions')
    @auth
        <a href="{{ route('organization.subscription.index') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition shadow-lg text-sm">
            Upgrade Subscription &rarr;
        </a>
        <a href="{{ route('organization.dashboard') }}" class="w-full sm:w-auto bg-slate-700 hover:bg-slate-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm">
            Go to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
            Sign In
        </a>
    @endauth
@endsection
