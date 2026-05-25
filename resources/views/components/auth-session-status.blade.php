@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-teal']) }}>
        {{ $status }}
    </div>
@endif
