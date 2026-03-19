@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-slate_custom-200 focus:border-burgundy-500 focus:ring-burgundy-500 rounded-lg shadow-sm text-navy-800 placeholder-slate_custom-300']) !!}>
