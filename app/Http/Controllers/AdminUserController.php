<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * List all users with optional search, role filter, and pagination.
     *
     * We use a query builder (User::query()) rather than calling User::all()
     * so we can chain where() clauses conditionally before paginating.
     * ->withQueryString() tells the paginator to preserve any ?search= and
     * ?filter= parameters in the generated page-number links.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $filter = $request->input('filter', 'all');

        $query = User::query()->orderBy('name');

        // Full-text-style search across name and email.
        // We wrap both conditions in a single where() closure so the OR only
        // applies between name and email, not against the outer filter clauses.
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role / status filter
        match ($filter) {
            'super_admin' => $query->where('is_super_admin', true),
            'suspended'   => $query->whereNotNull('suspended_at'),
            'user'        => $query->where('is_super_admin', false)
                                   ->whereNull('suspended_at'),
            default       => null,  // 'all' — no additional constraint
        };

        // paginate(15) gives us 15 rows per page and a LengthAwarePaginator,
        // which knows the total record count and can render numbered page links.
        // withQueryString() appends ?search=&filter= to every page link.
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

        $user->save();

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Suspension
    // -------------------------------------------------------------------------

    /**
     * Suspend a user by setting their suspended_at timestamp to now.
     *
     * We explicitly block admins from suspending their own account (that would
     * be a confusing self-lockout), and from suspending other super admins
     * (privilege separation — super admins shouldn't be able to knock each
     * other out without going through a higher-level process).
     */
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

        return back()->with('success', "{$user->name}'s account has been suspended.");
    }

    /**
     * Restore a suspended user by clearing their suspended_at timestamp.
     */
    public function unsuspend(User $user)
    {
        $user->suspended_at = null;
        $user->save();

        return back()->with('success', "{$user->name}'s account has been restored.");
    }
}
