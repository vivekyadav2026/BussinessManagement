@extends('errors.minimal')

@section('title', 'Service Maintenance')
@section('code', '503')
@section('heading', 'Under Maintenance')
@section('message', 'Vyapaargo ERP is currently undergoing scheduled maintenance & system optimization. We will be back online shortly.')

@section('actions')
    <button onclick="window.location.reload()" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-6 py-3 rounded-xl transition text-sm">
        Check Status / Refresh
    </button>
@endsection
