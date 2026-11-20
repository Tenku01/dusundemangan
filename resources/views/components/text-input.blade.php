@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-field disabled:bg-gray-100 disabled:text-gray-500']) }}>
