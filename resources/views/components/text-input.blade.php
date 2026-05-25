@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border bg-bg text-text placeholder-muted rounded-md shadow-sm focus:border-accent focus:ring-accent']) }}>
