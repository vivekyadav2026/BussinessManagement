@props(['route', 'icon', 'active' => false])

@php
    $classes = $active
        ? 'group flex items-center px-3 py-2 text-sm font-semibold rounded-md bg-[var(--theme-active)] text-[var(--theme-active-text)] shadow-sm'
        : 'group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-white/10 transition-colors';
    $iconClasses = $active ? 'text-[var(--theme-active-text)]' : 'text-gray-400 group-hover:text-gray-200';
@endphp

<a href="{{ $route }}" class="{{ $classes }}">
    <span class="mr-3 flex-shrink-0 h-5 w-5 {{ $iconClasses }}">
        {!! $icon !!}
    </span>
    <span class="truncate">
        {{ $slot }}
    </span>
</a>
