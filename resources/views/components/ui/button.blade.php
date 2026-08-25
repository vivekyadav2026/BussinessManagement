@props(['color' => 'indigo', 'type' => 'button'])

@php
$colors = [
    'indigo' => 'bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:ring-indigo-500',
    'white' => 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-indigo-500',
    'red' => 'bg-red-600 border border-transparent text-white hover:bg-red-700 focus:ring-red-500',
    'green' => 'bg-green-600 border border-transparent text-white hover:bg-green-700 focus:ring-green-500',
];

$theme = $colors[$color] ?? $colors['indigo'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {$theme} transition duration-150 ease-in-out"]) }}>
    {{ $slot }}
</button>
