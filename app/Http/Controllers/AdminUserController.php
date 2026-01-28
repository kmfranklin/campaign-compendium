<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
