<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-danger-solid border border-transparent rounded-md font-semibold text-xs text-on-danger uppercase tracking-widest hover:bg-danger-hover active:bg-danger-hover/90 focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2 focus:ring-offset-bg transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
