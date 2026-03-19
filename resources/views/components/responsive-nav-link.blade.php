@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-burgundy-800 text-start text-base font-medium text-burgundy-800 bg-burgundy-50 focus:outline-none focus:text-burgundy-900 focus:bg-burgundy-100 focus:border-burgundy-800 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate_custom-500 hover:text-navy-800 hover:bg-slate_custom-100 hover:border-slate_custom-200 focus:outline-none focus:text-navy-800 focus:bg-slate_custom-100 focus:border-slate_custom-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
