<nav x-data="{ open: false }"
     class="bg-surface border-b-2 border-border h-24">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
    <div class="flex justify-between items-stretch h-full">

      <!-- Left cluster -->
      <div class="flex items-stretch">

        <!-- Logo -->
        <div class="shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <img src="/images/campaign-compendium-nav-logo.png"
                 alt="Campaign Compendium logo"
                 class="block h-20 w-auto" />
          </a>
        </div>

        <!-- Nav links -->
        <div class="hidden sm:flex sm:ms-10 items-center h-full space-x-8">

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

            <x-nav-link href="{{ route('compendium.npcs.index') }}" :active="request()->routeIs('compendium.npcs.*')">
              {{ __('Characters') }}
            </x-nav-link>

            <!-- Items Dropdown -->
            <x-dropdown align="left" width="48">
              <x-slot name="trigger">
                <button
                    class="flex items-center h-full px-1 border-b-2 text-sm font-medium
                           text-muted hover:text-text hover:border-border
                           focus:outline-none transition duration-150 ease-in-out
                           {{ request()->routeIs('items.*') ? 'border-purple-800' : 'border-transparent' }}">
                    <span class="pb-px">Items</span>
                </button>
              </x-slot>

              <x-slot name="content">
                <a href="{{ route('items.index') }}"
                   class="block px-4 py-2 text-sm text-text hover:bg-bg">
                  All Items
                </a>
                <a href="{{ route('srdItems.index') }}"
                   class="block px-4 py-2 text-sm text-text hover:bg-bg">
                  SRD Items
                </a>
                <a href="{{ route('customItems.index') }}"
                   class="block px-4 py-2 text-sm text-text hover:bg-bg">
                  Custom Items
                </a>
              </x-slot>
            </x-dropdown>
          @endauth
        </div>
      </div>

      <!-- Right cluster -->
      @auth
        <div class="hidden sm:flex sm:items-stretch sm:ms-6">

          @php
            $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
          @endphp

          <!-- Notifications -->
          <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center">
            <svg class="w-6 h-6 text-text hover:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>

            @if ($unreadCount > 0)
              <span class="absolute top-7 right-4 bg-purple-700 text-white text-xs font-semibold rounded-full px-1.5 py-0.5">
                {{ $unreadCount }}
              </span>
            @endif
          </a>

          <!-- User Dropdown -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="inline-flex items-center h-full px-3 text-text">
                <div>{{ Auth::user()->name }}</div>
                <div class="ms-1"></div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
              </x-dropdown-link>

              <x-dropdown-link href="{{ route('notifications.index') }}" :active="request()->routeIs('notifications.index.*')">
                {{ __('Notifications') }}
              </x-dropdown-link>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                  onclick="event.preventDefault(); this.closest('form').submit();">
                  {{ __('Log Out') }}
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        </div>
      @endauth

      @guest
        <div class="flex items-center gap-4">
          <a href="{{ route('login') }}" class="text-sm font-semibold text-text hover:text-purple-700">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="text-sm font-semibold text-white bg-purple-700 px-4 py-2 rounded hover:bg-purple-800">
            Sign Up
          </a>
        </div>
      @endguest

      <!-- Hamburger -->
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-muted hover:text-text hover:bg-bg focus:outline-none transition">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': ! open }"
                  class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': ! open, 'inline-flex': open }"
                  class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Responsive Navigation Menu -->
  <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-surface">
    <div class="pt-2 pb-3 space-y-1">
      <x-responsive-nav-link :href="route('srdItems.index')" :active="request()->routeIs('srdItems.*')">
        {{ __('SRD Items') }}
      </x-responsive-nav-link>

      @auth
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
          {{ __('Dashboard') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('campaigns.index')" :active="request()->routeIs('campaigns.*')">
          {{ __('Campaigns') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link href="{{ route('compendium.npcs.index') }}" :active="request()->routeIs('compendium.npcs.*')">
          {{ __('Characters') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.index')">
          {{ __('All Items') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('customItems.index')" :active="request()->routeIs('customItems.index')">
          {{ __('Custom Items') }}
        </x-responsive-nav-link>
      @endauth
    </div>

    @auth
      <div class="pt-4 pb-1 border-t border-border">
        <div class="px-4">
          <div class="font-medium text-base text-text">{{ Auth::user()->name }}</div>
          <div class="font-medium text-sm text-muted">{{ Auth::user()->email }}</div>
        </div>

        <div class="mt-3 space-y-1">
          <x-responsive-nav-link :href="route('profile.edit')">
            {{ __('Profile') }}
          </x-responsive-nav-link>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
                onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('Log Out') }}
            </x-responsive-nav-link>
          </form>
        </div>
      </div>
    @endauth

    @guest
      <div class="pt-4 pb-1 border-t border-border">
        <div class="px-4 space-y-1">
          <x-responsive-nav-link :href="route('login')" class="text-text hover:bg-bg">
            {{ __('Sign In') }}
          </x-responsive-nav-link>

          <x-responsive-nav-link :href="route('register')" class="text-text hover:bg-bg">
            {{ __('Register') }}
          </x-responsive-nav-link>
        </div>
      </div>
    @endguest
  </div>
</nav>
