<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-ghost py-2 px-5 text-xs']) }}>
    {{ $slot }}
</button>
