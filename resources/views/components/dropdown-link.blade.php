<<<<<<< HEAD
<a {{ $attributes->merge([
    'class' =>
        'block w-full px-4 py-2 text-start text-sm leading-5
         text-text hover:bg-bg focus:outline-none focus:bg-bg
         transition duration-150 ease-in-out bg-surface'
]) }}>
=======
<a
    {{ $attributes->merge([
        'class' =>
            'block w-full px-4 py-2 text-start text-sm leading-5
             text-gray-700 hover:text-gray-900 hover:bg-gray-100
             focus:outline-none focus:bg-gray-100
             dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-800
             transition duration-150 ease-in-out'
    ]) }}
>
>>>>>>> f69f6c8 (refactor(admin): add link for admin tools to nav and refactor links and dropdowns for consistent styling and UI)
    {{ $slot }}
</a>
