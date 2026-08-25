@props(['color' => 'gray'])

@php
$colors = [
    'gray' => 'bg-gray-100 text-gray-800',
    'green' => 'bg-green-100 text-green-800',
    'blue' => 'bg-blue-100 text-blue-800',
    'red' => 'bg-red-100 text-red-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
];

$theme = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$theme}"]) }}>
    {{ $slot }}
</span>
