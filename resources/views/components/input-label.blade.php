@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-gray-800 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
