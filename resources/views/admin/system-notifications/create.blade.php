@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-2xl">

    <div>
        <h1 class="text-2xl font-semibold text-text">New Notification</h1>
        <p class="mt-1 text-sm text-muted">
            This notification will appear as a dismissible banner for all authenticated users.
        </p>
    </div>

    @include('admin.system-notifications._form', [
        'action' => route('admin.notifications.store'),
        'method' => 'POST',
    ])

</div>
@endsection
