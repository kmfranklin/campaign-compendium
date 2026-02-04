@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'rows' => 3
])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-text mb-1">
        {{ $label }}
    </label>

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            {{ $attributes->merge([
                'class' => 'mt-1 block w-full rounded-md border-border bg-surface text-text shadow-sm focus:border-accent focus:ring-accent sm:text-sm'
            ]) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge([
                'class' => 'mt-1 block w-full rounded-md border-border bg-surface text-text shadow-sm focus:border-accent focus:ring-accent sm:text-sm'
            ]) }}
        >
    @endif

    @error($name)
        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
