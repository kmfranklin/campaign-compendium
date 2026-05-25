@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'ui-select mt-1'
        ]) }}
    >
        @if($placeholder)
            <option value="" disabled {{ old($name, $selected) === null ? 'selected' : '' }}>
                {{ $placeholder }}
            </option>
        @endif

        @foreach($options as $option)
            <option value="{{ $option }}" @selected(old($name, $selected) === $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>
</div>
