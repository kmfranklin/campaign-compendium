<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function signInAs(User $user)
    {
        session(['admin_id' => auth()->id()]);

        auth()->login($user);

        return redirect('/dashboard')
            ->with('success', "You are now signed in as {$user->name}.");
    }

    public function returnToAdmin()
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            abort(403, 'Not impersonating another user.');
        }

        auth()->loginUsingId($adminId);
        session()->forget('admin_id');

        return redirect('/admin')
            ->with('success', 'You have returned to your admin account.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'is_super_admin' => ['sometimes', 'boolean'],
            'force_email_verification' => ['sometimes', 'boolean'],
        ]);

        // Basic fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Optional password update
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Toggle super admin status
        if ($request->has('is_super_admin')) {
            $user->is_super_admin = (bool) $validated['is_super_admin'];
        }

        // Force email verification
        if ($request->boolean('force_email_verification')) {
            $user->email_verified_at = now();
        }

        $user->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated successfully.');
    }
}
