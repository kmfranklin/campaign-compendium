<div class="px-6 py-4 text-sm text-gray-700">
    <p class="font-medium text-gray-900">Campaign Update</p>
    <p class="text-gray-600">{{ $notification->data['message'] ?? 'No update details provided.' }}</p>

    <div class="text-xs text-gray-500 mt-1">
        Received {{ $notification->created_at->diffForHumans() }}
    </div>
</div>
