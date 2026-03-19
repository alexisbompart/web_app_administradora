@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-burgundy-800 text-sm font-medium leading-5 text-navy-800 focus:outline-none focus:border-burgundy-800 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate_custom-400 hover:text-navy-800 hover:border-slate_custom-200 focus:outline-none focus:text-navy-800 focus:border-slate_custom-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
