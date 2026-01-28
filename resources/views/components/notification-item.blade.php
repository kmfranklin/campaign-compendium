@props(['notification',
        'layout' => 'desktop',
])

@php
    $type = $notification->type;
@endphp

<div class="w-full">
    @switch($type)

        @case(\App\Models\Notification::TYPE_INVITE)
            @include('notifications.partials.invite',
            [
                'notification' => $notification,
                'layout' => $layout,
            ])
            @break

        @case(\App\Models\Notification::TYPE_SYSTEM)
            @include('notifications.partials.system',
            [
                'notification' => $notification,
                'layout' => $layout,
            ])
            @break

        @case(\App\Models\Notification::TYPE_CAMPAIGN_UPDATE)
            @include('notifications.partials.campaign-update',
            [
                'notification' => $notification,
                'layout' => $layout,
            ])
            @break

    @endswitch
</div>
