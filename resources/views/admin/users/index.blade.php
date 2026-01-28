@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-semibold text-gray-900">Users</h1>

    <p class="mt-2 text-gray-600">
        View all users and impersonate accounts.
    </p>

    <table class="mt-6 w-full text-left text-sm">
        <thead>
            <tr class="border-b">
                <th class="py-2">ID</th>
                <th class="py-2">Name</th>
                <th class="py-2">Email</th>
                <th class="py-2">Super Admin</th>
                <th class="py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr class="border-b">
                <td class="py-2">{{ $user->id }}</td>
                <td class="py-2">{{ $user->name }}</td>
                <td class="py-2">{{ $user->email }}</td>
                <td class="py-2">
                    {{ $user->is_super_admin ? 'Yes' : 'No' }}
                </td>
                <td class="py-2 text-right">
                    <form action="{{ route('admin.users.signInAs', $user->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="text-purple-700 hover:text-purple-900 text-sm font-medium">
                            Sign in as {{ $user->name }}
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
