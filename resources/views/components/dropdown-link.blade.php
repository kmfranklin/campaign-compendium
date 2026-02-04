<a {{ $attributes->merge([
    'class' =>
        'block w-full px-4 py-2 text-start text-sm leading-5
         text-text hover:bg-bg focus:outline-none focus:bg-bg
         transition duration-150 ease-in-out bg-surface'
]) }}>
    {{ $slot }}
</a>
