<html>
<head>
    <title>Campaign Compendium</title>
    <style>
        /* Light mode */
        :root {
            --color-bg: #f9fafb;
            --color-surface: #fff;
            --color-border: #e5e7eb;
            --color-text: #111827;
            --color-text-muted: #6b7280;
        }

        /* Dark mode */
        .dark {
            --color-bg: #111827;
            --color-surface: #1f2937;
            --color-border: #374151;
            --color-text: #f3f4f6;
            --color-text-muted: #9ca3af;
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

    {{-- Success Message --}}
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

    <footer class="bg-surface border-t border-border mt-12 py-6 text-center text-muted">
        © {{ date('Y') }} Campaign Compendium. All rights reserved.
    </footer>

</body>
</html>
