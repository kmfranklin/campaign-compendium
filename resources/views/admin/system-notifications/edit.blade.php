@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-2xl">

    <div>
        <h1 class="text-2xl font-semibold text-text">Edit Notification</h1>
        <p class="mt-1 text-sm text-muted">
            Changes take effect immediately for any user who has not yet dismissed this notification.
        </p>
    </div>

    @include('admin.system-notifications._form', [
        'action'   => route('admin.notifications.update', $notification),
        'method'   => 'PATCH',
        'editing'  => true,
    ])

</div>
@endsection
