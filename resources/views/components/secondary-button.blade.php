<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary btn-sm uppercase tracking-[0.18em]']) }}>
    {{ $slot }}
</button>
