@extends('layouts.app')

@section('content')
<div class="w-full max-w-4xl mx-auto py-10">
    <h1 class="text-2xl font-semibold text-gray-900">Super Admin Dashboard</h1>
    <p class="mt-4 text-gray-700">
        Welcome, {{ auth()->user()->name }}.
    </p>
</div>
@endsection
