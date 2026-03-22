<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin &mdash; Campaign Compendium</title>

    <style>
        :root {
            --color-bg: #f9fafb;
            --color-surface: #fff;
            --color-border: #e5e7eb;
            --color-text: #111827;
            --color-text-muted: #6b7280;
        }
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

    {{-- Skip-to-content link for keyboard and screen reader users --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50
              bg-accent text-on-accent px-4 py-2 rounded text-sm font-semibold">
        Skip to main content
    </a>

    @include('layouts.navigation')

    @if (session('admin_id'))
        <div class="bg-yellow-500 text-black text-center py-2 text-sm"
             role="status"
             aria-live="polite">
            You are signed in as another user.
            <form action="{{ route('admin.returnToAdmin') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="underline font-semibold ml-2">
                    Return to Admin Account
                </button>
            </form>
        </div>
    @endif

    {{--
        Root Alpine component for the entire admin shell.

        State:
          expanded     — desktop sidebar is full-width (true) or icon-only rail (false).
                         Persisted to localStorage so it survives page navigation.
          mobileOpen   — the mobile/tablet sidebar drawer is open (true) or closed (false).
                         Always resets to false on page load (no persistence needed).

        toggleSidebar() — flips expanded and writes to localStorage. Called by the
                          collapse/expand buttons inside the sidebar partial.
    --}}
    <div x-data="{
             expanded: JSON.parse(localStorage.getItem('adminSidebar') ?? 'true'),
             mobileOpen: false,
             toggleSidebar() {
                 this.expanded = !this.expanded;
                 localStorage.setItem('adminSidebar', JSON.stringify(this.expanded));
             }
         }"
         class="flex flex-1 min-h-0 relative">

        {{--
            Mobile backdrop. A semi-transparent overlay that covers the content area
            when the mobile sidebar is open. Clicking it closes the drawer.
            lg:hidden ensures it's never shown on desktop (where no overlay is needed).
        --}}
        <div x-show="mobileOpen"
             @click="mobileOpen = false"
             class="fixed inset-0 z-20 bg-black/50 lg:hidden"
             x-transition:enter="transition-opacity duration-200 ease-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150 ease-in"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             aria-hidden="true">
        </div>

        {{--
            Mobile sidebar drawer. Fixed-position, slides in from the left.
            Uses role="dialog" + aria-modal="true" so screen readers treat it as
            a modal dialog (trapping virtual cursor inside while open).
            @keydown.escape closes it — standard keyboard interaction for dialogs.

            The inner x-data="{ expanded: true }" creates a child Alpine scope that
            overrides the parent's `expanded` variable for the sidebar-links partial.
            This ensures the mobile drawer always shows full labels, regardless of
            whether the desktop sidebar is currently collapsed.
        --}}
        <aside x-show="mobileOpen"
               x-transition:enter="transition-transform duration-300 ease-out"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition-transform duration-200 ease-in"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               @keydown.escape.window="mobileOpen = false"
               class="fixed inset-y-0 left-0 z-30 w-72 flex flex-col
                      bg-surface border-r border-border lg:hidden"
               role="dialog"
               aria-modal="true"
               aria-label="Admin navigation"
               :aria-hidden="(!mobileOpen).toString()">

            {{-- Mobile drawer header --}}
            <div class="flex items-center justify-between px-4 py-4 border-b border-border">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent dark:text-purple-300 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-widest text-accent dark:text-purple-300">
                        Admin Panel
                    </span>
                </div>
                <button @click="mobileOpen = false"
                        class="p-1 rounded text-muted hover:text-text hover:bg-bg
                               focus:outline-none focus:ring-2 focus:ring-accent
                               transition-colors duration-150"
                        aria-label="Close admin navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{--
                Mobile nav links. x-data="{ expanded: true }" creates a child scope
                that overrides the parent's `expanded`, so the sidebar-links partial
                always renders in fully-expanded mode here.
            --}}
            <nav class="flex-1 px-3 py-4 overflow-y-auto" aria-label="Admin tools">
                <div x-data="{ expanded: true }">
                    @include('admin.partials.sidebar-links')
                </div>
            </nav>

            {{-- Mobile drawer footer --}}
            <div class="border-t border-border p-4">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 text-sm text-muted hover:text-text
                          transition-colors duration-150">
                    <svg class="w-4 h-4 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to App
                </a>
            </div>
        </aside>

        {{-- Desktop sidebar (hidden below lg) --}}
        @include('admin.partials.sidebar')

        {{-- Content column --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{--
                Mobile admin bar. Only visible below lg. Contains the hamburger
                button that opens the mobile drawer, and an "Admin Panel" label
                so the user always knows they're in the admin area even without
                the desktop sidebar.
            --}}
            <div class="lg:hidden flex items-center gap-3 px-4 py-3
                        bg-surface border-b border-border shrink-0">
                <button @click="mobileOpen = true"
                        class="p-1.5 rounded text-muted hover:text-text hover:bg-bg
                               focus:outline-none focus:ring-2 focus:ring-accent
                               transition-colors duration-150"
                        aria-label="Open admin navigation"
                        aria-haspopup="dialog">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-xs font-bold uppercase tracking-widest text-accent dark:text-purple-300">
                    Admin Panel
                </span>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="px-8 pt-6" role="status">
                    <div class="p-3 bg-surface text-text border border-border rounded text-sm">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="px-8 pt-6" role="status">
                    <div class="p-3 bg-surface text-text border border-border rounded text-sm">
                        {{ session('info') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="px-8 pt-6" role="alert">
                    <div class="p-3 bg-surface text-text border border-border rounded text-sm">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <main id="main-content" tabindex="-1" class="flex-1 px-4 sm:px-8 py-8">
                @yield('content')
            </main>

        </div>
    </div>

    <footer class="bg-surface border-t border-border py-4 text-center text-muted text-sm">
        &copy; {{ date('Y') }} Campaign Compendium. All rights reserved.
    </footer>

</body>
</html>
