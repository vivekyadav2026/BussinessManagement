@props(['route', 'icon', 'active' => false])

@php
    $classes = $active
        ? 'group flex items-center px-3 py-2 text-sm font-semibold rounded-md bg-[var(--theme-active)] text-[var(--theme-active-text)] shadow-sm'
        : 'group flex items-center px-3 py-2 text-sm font-medium rounded-md text-[var(--theme-text)] hover:text-[var(--theme-hover-text)] hover:bg-[var(--theme-hover)] transition-colors';
    $iconClasses = $active ? 'text-[var(--theme-active-text)]' : 'text-gray-400 group-hover:text-[var(--theme-hover-text)]';
@endphp

<a href="{{ $route }}" class="{{ $classes }}">
    <span class="mr-3 flex-shrink-0 h-5 w-5 {{ $iconClasses }}">
        {!! $icon !!}
    </span>
    <span class="truncate">
        {{ $slot }}
    </span>
</a>
