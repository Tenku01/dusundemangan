@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-[#CEA042] text-start text-sm font-semibold text-emas-900 bg-emas-50 focus:outline-none focus:text-emas-950 focus:bg-emas-100 focus:border-emas-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
