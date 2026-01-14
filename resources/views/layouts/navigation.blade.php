<nav x-data="{ open: false }"
     class="bg-white dark:bg-gray-900 border-b-2 border-gray-100 dark:border-gray-700 h-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
    <div class="flex justify-between items-stretch h-full">
      <!-- left cluster -->
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

            <x-dropdown align="left" width="48">
              <x-slot name="trigger">
                <button
                    class="flex items-center h-full px-1 border-b-2
                    text-sm font-medium
                    text-gray-500 hover:text-gray-700
                    hover:border-gray-300
                    focus:outline-none
                    transition duration-150 ease-in-out
                    dark:text-gray-300 dark:hover:text-gray-100
                    {{ request()->routeIs('items.*') ? 'border-purple-800 dark:border-purple-500' : 'border-transparent' }}">

                    <span class="pb-px">Items</span>
                </button>
              </x-slot>


              <x-slot name="content">
                <a href="{{ route('items.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:text-white dark:bg-gray-600 dark:hover:bg-gray-800">All Items</a>
                <a href="{{ route('srdItems.index') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:text-white dark:bg-gray-600 dark:hover:bg-gray-800">SRD Items</a>
                <a href="{{ route('customItems.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:text-white dark:bg-gray-600 dark:hover:bg-gray-800">Custom Items</a>
              </x-slot>
            </x-dropdown>
          @endauth
        </div>
      </div>

      <!-- right cluster -->
      @auth
        <div class="hidden sm:flex sm:items-stretch sm:ms-6">
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="inline-flex items-center h-full px-3 text-gray-700 dark:text-gray-200">
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
          <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-800 hover:text-purple-700 dark:text-gray-100 dark:hover:text-amber-400">Login</a>
          <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-purple-700 px-4 py-2 rounded hover:bg-purple-800 dark:bg-purple-600 dark:hover:bg-purple-700">Sign Up</a>
        </div>
      @endguest



            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out dark:text-gray-300 dark:hover:text-gray-200 dark:hover:bg-gray-800 dark:focus:bg-gray-800">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-900">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Always visible -->
            <x-responsive-nav-link :href="route('srdItems.index')" :active="request()->routeIs('srdItems.*')">
                {{ __('SRD Items') }}
            </x-responsive-nav-link>

            <!-- Only visible if signed in -->
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

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
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
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 space-y-1">
                    <x-responsive-nav-link :href="route('login')"
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
                        {{ __('Sign In') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')"
                        class="text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endguest
    </div>
</nav>
