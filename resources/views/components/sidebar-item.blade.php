@props(['route', 'icon', 'active' => false])

@php
    $classes = $active
        ? 'group flex items-center py-2 text-sm font-semibold rounded-xl bg-[var(--theme-active)] text-[var(--theme-active-text)] shadow-sm'
        : 'group flex items-center py-2 text-sm font-medium rounded-xl text-[var(--theme-text)] hover:text-[var(--theme-hover-text)] hover:bg-[var(--theme-hover)] transition-colors';
    $iconClasses = $active ? 'text-[var(--theme-active-text)]' : 'text-gray-400 group-hover:text-[var(--theme-hover-text)]';
@endphp

<a href="{{ $route }}" class="{{ $classes }}" :class="typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed ? 'justify-center px-2' : 'px-3'" title="{{ $slot }}">
    <span class="flex-shrink-0 h-5 w-5 {{ $iconClasses }}" :class="typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed ? 'mr-0' : 'mr-3'">
        {!! $icon !!}
    </span>
    <span x-show="typeof sidebarCollapsed === 'undefined' || !sidebarCollapsed" class="truncate">
        {{ $slot }}
    </span>
</a>
