<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Campaign Compendium</title>
    <style>
        /* Light mode */
        :root {
            --color-bg: #f9fafb;
            --color-surface: #fff;
            --color-border: #e5e7eb;
            --color-text: #111827;
            --color-text-muted: #6b7280;
            --color-accent: #6d28d9;       /* violet-700 — 7:1 on white ✓ */
            --color-accent-hover: #5b21b6; /* violet-800 — 9:1 on white ✓ */
            --color-on-accent: #fff;
            --color-hover: #f5f3ff;        /* violet-50 — subtle row highlight */
        }

        /* Dark mode */
        .dark {
            --color-bg: #111827;
            --color-surface: #1f2937;
            --color-border: #374151;
            --color-text: #f3f4f6;
            --color-text-muted: #9ca3af;
            --color-accent: #a78bfa;       /* violet-400 — 5.4:1 on surface ✓ */
            --color-accent-hover: #c4b5fd; /* violet-300 — 8:1 on surface ✓  */
            --color-on-accent: #1e1b4b;    /* deep indigo for text on light accent bg */
            --color-hover: #374151;        /* gray-700 — subtle row highlight */
        }
    </style>

    <script>
        const stored = localStorage.getItem('theme');

        if (stored === 'dark' ||
            (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-bg text-text font-sans flex flex-col min-h-screen">

    @include('layouts.navigation')

    @include('partials.system-notification-banners')

    @if (session('admin_id'))
        <div class="bg-yellow-500 text-black text-center py-2">
            You are signed in as another user.
            <form action="{{ route('admin.returnToAdmin') }}" method="POST" class="inline">
                @csrf
                <button class="underline font-semibold">Return to Admin Account</button>
            </form>
        </div>
    @endif

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-4 mb-6 px-4">
            <div class="mb-4 p-3 bg-surface text-text border border-border rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Info Message --}}
    @if (session('info'))
        <div class="max-w-3xl mx-auto mb-6 px-4">
            <div class="mb-4 p-3 bg-surface text-text border border-border rounded">
                {{ session('info') }}
            </div>
        </div>
    @endif

    {{-- Error Message --}}
    @if ($errors->any())
        <div class="max-w-3xl mx-auto mb-6 px-4">
            <div class="mb-4 p-3 bg-surface text-text border border-border rounded">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    @hasSection('hero')
        @yield('hero')
    @endif

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        @yield('content')
    </main>

    <footer class="bg-surface border-t border-border mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-muted">© {{ date('Y') }} Campaign Compendium. All rights reserved.</p>
            <nav aria-label="Footer navigation">
                <ul class="flex items-center gap-6 list-none m-0 p-0">
                    <li>
                        <a href="{{ route('dice-roller') }}"
                           class="text-sm text-muted hover:text-text transition-colors duration-150 focus:outline-none focus:underline">
                            Dice Roller
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}"
                           class="text-sm text-muted hover:text-text transition-colors duration-150 focus:outline-none focus:underline">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/kmfranklin/campaign-compendium"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="text-sm text-muted hover:text-text transition-colors duration-150 focus:outline-none focus:underline">
                            GitHub
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </footer>

</body>
</html>
