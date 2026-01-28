@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-3xl font-semibold text-gray-900">Super Admin Dashboard</h1>

    <p class="mt-4 text-gray-700">
        Welcome, {{ auth()->user()->name }}. Choose a tool to begin.
    </p>

    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <a href="{{ route('admin.users.index') }}"
           class="p-6 bg-white border rounded-lg shadow hover:bg-gray-50 transition">
            <h2 class="text-xl font-semibold">User Management</h2>
            <p class="text-sm text-gray-600 mt-2">View all users and impersonate accounts.</p>
        </a>

        {{-- <a href="{{ route('admin.notifications.index') }}"
           class="p-6 bg-white border rounded-lg shadow hover:bg-gray-50 transition"> --}}
            <h2 class="text-xl font-semibold">System Notifications</h2>
            <p class="text-sm text-gray-600 mt-2">Send announcements to users.</p>
        </a>
    </div>
</div>
@endsection
