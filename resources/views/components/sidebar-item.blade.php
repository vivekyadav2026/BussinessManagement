@props(['route', 'icon', 'active' => false])

@php
    $classes = $active
        ? 'group flex items-center px-3 py-2 text-sm font-semibold rounded-md bg-yellow-500 text-gray-900 shadow-sm'
        : 'group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-slate-800/50 transition-colors';
    $iconClasses = $active ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-200';
@endphp

<a href="{{ $route }}" class="{{ $classes }}">
    <span class="mr-3 flex-shrink-0 h-5 w-5 {{ $iconClasses }}">
        {!! $icon !!}
    </span>
    <span class="truncate">
        {{ $slot }}
    </span>
</a>
