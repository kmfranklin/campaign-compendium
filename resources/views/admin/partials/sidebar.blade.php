{{--
    Desktop-only sidebar. Hidden below lg breakpoint; the mobile drawer (in
    admin.blade.php) handles smaller screens.

    This element reads `expanded` and `toggleSidebar()` from the Alpine x-data
    scope set on the flex container in admin.blade.php. No x-data here — it
    deliberately inherits the parent scope so the layout can also react to the
    same state (e.g. adjusting the content column).

    Width transitions between w-64 (expanded) and w-16 (collapsed, icon-only rail).
    overflow-hidden prevents link text from spilling out during the animation.
--}}
<aside class="hidden lg:flex flex-col shrink-0 bg-surface border-r border-border
              overflow-hidden transition-[width] duration-300 ease-in-out"
       :class="expanded ? 'w-64' : 'w-16'"
       aria-label="Admin navigation">

    {{--
        Header area: "Admin Panel" badge + collapse toggle button.

        In expanded state: shield icon + "ADMIN PANEL" text + collapse arrow on the right.
        In collapsed state: only the shield icon, centered.

        The toggle button uses aria-expanded to communicate the current state to screen
        readers, and aria-label switches between "Collapse sidebar" and "Expand sidebar".
        The chevron icon rotates 180° between the two states using a CSS transform.

        Contrast fix: text-accent (#6d28d9) has only ~2.1:1 contrast on dark surface
        (#1f2937). dark:text-purple-300 (#c4b5fd) gives 8.2:1 — WCAG AAA.
    --}}
    <div class="flex items-center border-b border-border shrink-0"
         :class="expanded ? 'px-4 py-4 justify-between' : 'px-0 py-4 justify-center'">

        {{-- Badge: icon always visible; label text fades when collapsed --}}
        <div class="flex items-center gap-2 min-w-0">
            <svg class="w-4 h-4 flex-shrink-0 text-accent dark:text-purple-300"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span x-show="expanded"
                  x-transition:enter="transition-opacity duration-150 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="text-xs font-bold uppercase tracking-widest text-accent dark:text-purple-300 whitespace-nowrap">
                Admin Panel
            </span>
        </div>

        {{--
            Collapse / expand toggle. x-show="expanded" hides it when collapsed so
            the icon-only rail stays clean — the user can click any icon area or the
            shield to... actually we can't do that cleanly. Instead, we place a small
            expand button that's always visible in collapsed state.
        --}}
        <button @click="toggleSidebar()"
                class="p-1 rounded text-muted hover:text-text hover:bg-bg
                       focus:outline-none focus:ring-2 focus:ring-accent flex-shrink-0
                       transition-colors duration-150"
                :class="expanded ? 'ml-auto' : 'hidden'"
                :aria-expanded="expanded.toString()"
                :aria-label="expanded ? 'Collapse sidebar' : 'Expand sidebar'">
            {{-- Chevron points left when expanded (collapses), right when collapsed --}}
            <svg class="w-4 h-4 transition-transform duration-300"
                 :class="expanded ? '' : 'rotate-180'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{--
        Expand button for collapsed state. Sits below the header, centered, so
        keyboard and mouse users can easily expand the sidebar from the icon rail.
    --}}
    <button x-show="!expanded"
            @click="toggleSidebar()"
            class="mx-auto mt-3 p-1.5 rounded text-muted hover:text-text hover:bg-bg
                   focus:outline-none focus:ring-2 focus:ring-accent flex-shrink-0
                   transition-colors duration-150"
            aria-label="Expand sidebar">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5l7 7-7 7" />
        </svg>
    </button>

    {{--
        Navigation links. The shared partial reads `expanded` from this Alpine scope
        to toggle label text visibility and icon centering.
    --}}
    <nav class="flex-1 py-4 overflow-hidden"
         :class="expanded ? 'px-3' : 'px-2'"
         aria-label="Admin tools">
        @include('admin.partials.sidebar-links')
    </nav>

    {{--
        "Back to App" footer link. Same fade-out treatment for the label text.
        In collapsed state, only the arrow icon is shown, centered.
    --}}
    <div class="border-t border-border shrink-0"
         :class="expanded ? 'p-4' : 'py-4 flex justify-center'">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-2 text-sm text-muted hover:text-text
                  transition-colors duration-150"
           :class="expanded ? '' : 'justify-center'"
           :title="expanded ? null : 'Back to App'">
            <svg class="w-4 h-4 flex-shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span x-show="expanded"
                  x-transition:enter="transition-opacity duration-150 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="whitespace-nowrap">
                Back to App
            </span>
        </a>
    </div>

</aside>
