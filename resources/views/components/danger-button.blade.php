<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-red-50 hover:bg-red-100 text-red-700 border border-red-100 py-2 px-5 text-xs font-bold']) }}>
    {{ $slot }}
</button>
