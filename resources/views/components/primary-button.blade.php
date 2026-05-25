<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-sm uppercase tracking-[0.18em]']) }}>
    {{ $slot }}
</button>
