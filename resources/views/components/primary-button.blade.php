<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-gold py-2 px-5 text-xs']) }}>
    {{ $slot }}
</button>
