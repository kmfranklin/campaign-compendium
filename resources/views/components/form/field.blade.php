@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'rows' => 3
])

<div class="mb-4">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
    </label>

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            {{ $attributes->merge([
                'class' => 'ui-textarea mt-1'
            ]) }}
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge([
                'class' => 'ui-field mt-1'
            ]) }}
        >
    @endif

    @error($name)
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
