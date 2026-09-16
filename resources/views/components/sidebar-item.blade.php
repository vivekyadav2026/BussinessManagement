@props(['route', 'icon', 'active' => false, 'target' => '_self'])

@php
    $classes = $active
        ? 'group flex items-center py-2 text-sm font-semibold rounded-lg bg-white/[0.08] text-amber-400 border border-amber-400/20 shadow-xs'
        : 'group flex items-center py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-white/[0.05] transition-all';
    $iconClasses = $active ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-200';
    $cleanLabel = trim(strip_tags($slot));
@endphp

<a href="{{ $route }}" target="{{ $target }}" class="{{ $classes }}" :class="typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed ? 'justify-center px-2' : 'px-3'" :title="typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed ? '{{ $cleanLabel }}' : ''">
    <span class="flex-shrink-0 h-4 w-4 {{ $iconClasses }}" :class="typeof sidebarCollapsed !== 'undefined' && sidebarCollapsed ? 'mr-0' : 'mr-2.5'">
        {!! $icon !!}
    </span>
    <span x-show="typeof sidebarCollapsed === 'undefined' || !sidebarCollapsed" class="truncate text-[13px]">
        {{ $cleanLabel }}
    </span>
</a>
