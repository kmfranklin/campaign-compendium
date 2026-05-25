<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#120f1c">

    <title>Campaign Compendium</title>

    <script>
        const stored = localStorage.getItem('theme');

        if (stored === 'dark' ||
            (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-text antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="flex justify-center">
                <a href="/" class="inline-flex items-center justify-center">
                    <img
                        src="/images/campaign-compendium-nav-logo.png"
                        alt="Campaign Compendium logo"
                        class="h-20 w-auto"
                    />
                </a>
            </div>

            <div class="mt-8 overflow-hidden rounded-2xl border border-border bg-surface/95 px-6 py-6 shadow-2xl shadow-black/10 backdrop-blur">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
