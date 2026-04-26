<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    /**
     * If the authenticated user's account is suspended, immediately log them
     * out, invalidate their session, and redirect to the login page with an
     * explanatory error.
     *
     * We run this on every web request rather than only at login so that a
     * user who is suspended while already logged in is booted on their very
     * next page load — they won't see any protected content in between.
     *
     * The auth()->check() guard makes this a no-op for guests, so it's safe
     * to register on the global web middleware stack.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isSuspended()) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact an administrator.',
            ]);
        }

        return $next($request);
    }
}
