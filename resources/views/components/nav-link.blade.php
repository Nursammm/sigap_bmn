@props(['active' => false, 'icon' => 'home'])

@php
$classes = $active
    ? 'bg-gray-300 text-gray-700'
    : 'text-gray-300 hover:bg-white/5 hover:text-white';
@endphp

<a {{ $attributes->merge([
        'class' => "$classes group flex items-center rounded-md px-3 py-2 font-medium",
        'aria-current' => $active ? 'page' : null
    ]) }}>

    <x-dynamic-component 
        :component="'icons.' . $icon . ($active ? '-solid' : '-outline')" 
        class="mr-3 w-5 h-5 flex-shrink-0" 
    />
    
    <span class="text-sm font-medium">
        {{ $slot }}
    </span>
</a>