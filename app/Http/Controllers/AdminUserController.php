<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * List all users with optional search, role filter, and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $filter = $request->input('filter', 'all');

        $query = User::query()->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        match ($filter) {
            'super_admin' => $query->where('is_super_admin', true),
            'suspended'   => $query->whereNotNull('suspended_at'),
            'user'        => $query->where('is_super_admin', false)
                                   ->whereNull('suspended_at'),
            default       => null,
        };

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users'  => $users,
            'search' => $search,
            'filter' => $filter,
        ]);
    }

    // -------------------------------------------------------------------------
    // Impersonation
    // -------------------------------------------------------------------------

    public function signInAs(User $user)
    {
        session(['admin_id' => auth()->id()]);

        // Log before switching auth, so auth()->id() still returns the admin.
        ActivityLog::record(
            ActivityLog::EVENT_IMPERSONATION_STARTED,
            auth()->user()->name . ' started impersonating ' . $user->name,
            $user
        );

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

        // Capture the impersonated user's name before switching back.
        $impersonatedUser = auth()->user();

        auth()->loginUsingId($adminId);
        session()->forget('admin_id');

        // Log after switching back, so auth()->id() now reflects the admin.
        ActivityLog::record(
            ActivityLog::EVENT_IMPERSONATION_ENDED,
            auth()->user()->name . ' ended impersonation of ' . $impersonatedUser->name,
            $impersonatedUser
        );

        return redirect('/admin')
            ->with('success', 'You have returned to your admin account.');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

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

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->has('is_super_admin')) {
            $user->is_super_admin = (bool) $validated['is_super_admin'];
        }

        if ($request->boolean('force_email_verification')) {
            $user->email_verified_at = now();
        }

        // Capture which fields actually changed BEFORE save() — Eloquent
        // clears the "dirty" state once the model is saved, so calling
        // getDirty() after save() would return an empty array.
        // array_keys() strips the new values (we don't want to log those)
        // and leaves us with just the field names: ['name', 'email', ...].
        $changedFields = array_keys($user->getDirty());

        $user->save();

        if (!empty($changedFields)) {
            ActivityLog::record(
                ActivityLog::EVENT_USER_UPDATED,
                auth()->user()->name . ' updated ' . $user->name
                    . ' (' . implode(', ', $changedFields) . ')',
                $user,
                ['changed_fields' => $changedFields]
            );
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Suspension
    // -------------------------------------------------------------------------

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Admin accounts cannot be suspended.');
        }

        $user->suspended_at = now();
        $user->save();

        ActivityLog::record(
            ActivityLog::EVENT_USER_SUSPENDED,
            auth()->user()->name . ' suspended ' . $user->name,
            $user
        );

        return back()->with('success', "{$user->name}'s account has been suspended.");
    }

    public function unsuspend(User $user)
    {
        $user->suspended_at = null;
        $user->save();

        ActivityLog::record(
            ActivityLog::EVENT_USER_UNSUSPENDED,
            auth()->user()->name . ' restored ' . $user->name . "'s account",
            $user
        );

        return back()->with('success', "{$user->name}'s account has been restored.");
    }
}
