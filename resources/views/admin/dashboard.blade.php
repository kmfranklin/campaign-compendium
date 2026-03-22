@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-3xl font-semibold text-primary">
        Admin Dashboard
    </h1>

    <p class="mt-4 text-secondary">
        Welcome, {{ auth()->user()->name }}. Choose a tool to begin.
    </p>

    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- User Management --}}
        <a href="{{ route('admin.users.index') }}"
           class="p-6 bg-surface border border-base rounded-lg shadow
                  hover:bg-surface-hover transition">
            <h2 class="text-xl font-semibold text-primary">User Management</h2>
            <p class="text-sm text-secondary mt-2">
                View and manage all user accounts.
            </p>
        </a>

        {{-- System Notifications
        <a href="{{ route('admin.notifications.index') }}"
           class="p-6 bg-surface border border-base rounded-lg shadow
                  hover:bg-surface-hover transition"> --}}
            <h2 class="text-xl font-semibold text-primary">System Notifications</h2>
            <p class="text-sm text-secondary mt-2">
                Send announcements to users.
            </p>
        </a>

    </div>
</div>
@endsection
