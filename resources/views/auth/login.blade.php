<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (!empty($invite))
        <div class="mb-6 rounded-lg border border-accent/30 bg-accent/10 px-4 py-3 text-sm text-text">
            Sign in with <strong>{{ $invite->email }}</strong> to join <strong>{{ $invite->campaign->name }}</strong>.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        @if (!empty($invite))
            <input type="hidden" name="invite_token" value="{{ $invite->token }}">
        @endif

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $invite->email ?? null)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-border bg-bg text-accent shadow-sm focus:ring-accent" name="remember">
                <span class="ms-2 text-sm text-muted">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-muted hover:text-text rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent focus:ring-offset-bg" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 border-t border-border pt-4 text-center text-sm text-muted">
        New to Campaign Compendium?
        <a class="font-medium text-accent hover:text-accent-hover underline underline-offset-2 rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg"
           href="{{ !empty($invite) ? route('register', ['invite' => $invite->token]) : route('register') }}">
            Create an account
        </a>
    </div>
</x-guest-layout>
