@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-text mb-1">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'mt-1 block w-full rounded-md border-border bg-surface text-text shadow-sm focus:border-accent focus:ring-accent sm:text-sm'
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
