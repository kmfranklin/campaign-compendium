<a
    {{ $attributes->merge([
        'class' =>
            'block w-full px-4 py-2 text-start text-sm leading-5
             text-muted hover:text-text hover:bg-bg-elevated
             focus:outline-none focus:bg-bg-elevated
             transition duration-150 ease-in-out'
    ]) }}
>
    {{ $slot }}
</a>
