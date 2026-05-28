<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CampaignInvite;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $invite = null;

        if ($request->filled('invite')) {
            $invite = CampaignInvite::where('token', $request->query('invite'))->first();
        }

        return view('auth.register', [
            'invite' => $invite,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $invite = null;

        if ($request->filled('invite_token')) {
            $invite = CampaignInvite::where('token', $request->input('invite_token'))->first();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_token' => ['nullable', 'string'],
        ]);

        if ($invite && strcasecmp($request->email, $invite->email) !== 0) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'This invite is only valid for the invited email address.',
                ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($invite) {
            if ($invite->isPending() && ! $invite->isExpired()) {
                $invite->acceptFor($user);

                return redirect()
                    ->route('campaigns.show', $invite->campaign)
                    ->with('success', 'Your account has been created and you have joined the campaign.');
            }

            return redirect()
                ->route('invites.show', $invite->token)
                ->withErrors(['invite' => 'This invite is no longer active.']);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
