<nav
    x-data="{ open: false }"
    class="bg-white dark:bg-gray-900 border-b-2 border-gray-100 dark:border-gray-700"
>
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Top row -->
        <div class="flex justify-between items-center h-24">
            <!-- Left cluster -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img
                            src="/images/campaign-compendium-nav-logo.png"
                            alt="Campaign Compendium logo"
                            class="block h-20 w-auto"
                        />
                    </a>
                </div>

                <!-- Desktop nav -->
                <div class="hidden sm:flex sm:ms-10 items-center space-x-8">
                    @guest
                        <x-nav-link href="{{ route('srdItems.index') }}" :active="request()->routeIs('srdItems.*')">
                            {{ __('SRD Items') }}
                        </x-nav-link>
                    @endguest

                    @auth
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('campaigns.index') }}" :active="request()->routeIs('campaigns.*')">
                            {{ __('Campaigns') }}
                        </x-nav-link>

                        <x-nav-link
                            href="{{ route('compendium.npcs.index') }}"
                            :active="request()->routeIs('compendium.npcs.*')"
                        >
                            {{ __('Characters') }}
                        </x-nav-link>

                        @php
                            $itemsDropdownActive =
                                request()->routeIs('items.*') ||
                                request()->routeIs('customItems.*') ||
                                request()->routeIs('srdItems.*');

                            $userDropdownActive =
                                request()->routeIs('profile.*') ||
                                request()->routeIs('notifications.*') ||
                                request()->routeIs('admin.*');
                        @endphp

                        <!-- Items dropdown -->
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-1 border-b-2 h-24 text-sm font-medium leading-5
                                           {{ $itemsDropdownActive
                                               ? 'border-purple-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-purple-900'
                                               : 'border-transparent text-gray-500 dark:text-gray-100 hover:text-gray-700 dark:hover:text-amber-400 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-700 dark:focus:text-gray-200 focus:border-gray-300 dark:focus:border-gray-600' }}
                                           transition duration-150 ease-in-out"
                                >
                                    <span>Items</span>

                                    <svg
                                        class="ms-2 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('items.index')">
                                    {{ __('All Items') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('srdItems.index')">
                                    {{ __('SRD Items') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('customItems.index')">
                                    {{ __('Custom Items') }}
                                </x-dropdown-link>

                                @if (session('admin_id'))
                                    <form method="POST" action="{{ route('admin.returnToAdmin') }}" class="m-0">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm
                                                   text-gray-700 hover:text-gray-900 hover:bg-gray-100
                                                   dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-800
                                                   transition duration-150 ease-in-out"
                                        >
                                            Return to Admin Account
                                        </button>
                                    </form>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    @endauth
                </div>
            </div>

            <!-- Right cluster -->
            <div class="flex items-center gap-4">
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-semibold text-gray-800 hover:text-purple-700 dark:text-gray-100 dark:hover:text-amber-400"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="text-sm font-semibold text-white bg-accent px-4 py-2 rounded hover:bg-accent-hover transition-colors duration-150"
                    >
                        Sign Up
                    </a>
                @endguest

                @auth
                    @php
                        $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                    @endphp

                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center">
                        <svg
                            class="w-6 h-6 text-gray-700 hover:text-purple-700 dark:text-gray-200 dark:hover:text-amber-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>

                        @if ($unreadCount > 0)
                            <span
                                class="absolute -top-1 -right-2 bg-accent text-white text-xs font-semibold rounded-full px-1.5 py-0.5"
                            >
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User dropdown (styled to match Items dropdown trigger) -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="inline-flex items-center px-1 border-b-2 h-24 text-sm font-medium leading-5
                                       {{ $userDropdownActive
                                           ? 'border-purple-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-purple-900'
                                           : 'border-transparent text-gray-500 dark:text-gray-100 hover:text-gray-700 dark:hover:text-amber-400 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-700 dark:focus:text-gray-200 focus:border-gray-300 dark:focus:border-gray-600' }}
                                       transition duration-150 ease-in-out"
                            >
                                <span>{{ Auth::user()->name }}</span>

                                <svg
                                    class="ms-2 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (auth()->user()->is_super_admin)
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('Admin Dashboard') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('notifications.index')">
                                {{ __('Notifications') }}
                            </x-dropdown-link>

                            @if (session('admin_id'))
                                <form method="POST" action="{{ route('admin.returnToAdmin') }}" class="m-0">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm
                                               text-gray-700 hover:text-gray-900 hover:bg-gray-100
                                               dark:text-gray-200 dark:hover:text-white dark:hover:bg-gray-800
                                               transition duration-150 ease-in-out"
                                    >
                                        Return to Admin Account
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <x-dropdown-link
                                    :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                <!-- Hamburger (mobile) -->
                <div class="sm:hidden">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-gray-500 hover:text-gray-700 hover:bg-gray-100
                               focus:outline-none focus:bg-gray-100 focus:text-gray-700
                               transition duration-150 ease-in-out
                               dark:text-gray-300 dark:hover:text-gray-200 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                        aria-label="Toggle navigation"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{ 'hidden': open, 'inline-flex': !open }"
                                class="inline-flex"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ 'hidden': !open, 'inline-flex': open }"
                                class="hidden"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-cloak class="sm:hidden border-t border-gray-200 dark:border-gray-700">
            <div class="py-3 space-y-1">
                @guest
                    <x-responsive-nav-link :href="route('srdItems.index')" :active="request()->routeIs('srdItems.*')">
                        {{ __('SRD Items') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Sign In') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endguest

                @auth
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('campaigns.index')" :active="request()->routeIs('campaigns.*')">
                        {{ __('Campaigns') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link
                        :href="route('compendium.npcs.index')"
                        :active="request()->routeIs('compendium.npcs.*')"
                    >
                        {{ __('Characters') }}
                    </x-responsive-nav-link>

                    <div class="px-4 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Items
                    </div>

                    <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')">
                        {{ __('All Items') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('srdItems.index')" :active="request()->routeIs('srdItems.*')">
                        {{ __('SRD Items') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('customItems.index')" :active="request()->routeIs('customItems.*')">
                        {{ __('Custom Items') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                        {{ __('Notifications') }}
                    </x-responsive-nav-link>

                    @if (auth()->user()->is_super_admin)
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-responsive-nav-link>
                    @endif

                    @if (session('admin_id'))
                        <form method="POST" action="{{ route('admin.returnToAdmin') }}" class="m-0">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                Return to Admin Account
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <x-responsive-nav-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>

                    <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3 px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-100">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
