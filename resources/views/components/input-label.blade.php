@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-navy-800']) }}>
    {{ $value ?? $slot }}
</label>
