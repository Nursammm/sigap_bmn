@props(['size' => 'md']) {{-- xs, sm, md, lg, xl --}}

@php
  // Mapping aman untuk Tailwind (tidak dinamis agar tidak kena purge)
  $sizes = [
    'xs' => 'w-4 h-4',
    'sm' => 'w-5 h-5',
    'md' => 'w-6 h-6',   // <- default sama seperti ikon menu lain
    'lg' => 'w-7 h-7',
    'xl' => 'w-8 h-8',
  ];
  $dim = $sizes[$size] ?? $sizes['md'];
@endphp

<svg {{ $attributes->merge(['class' => "$dim shrink-0"]) }}
     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 13a9 9 0 1 0-1-3M2 11h6V5"/>
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v6l4 2"/>
</svg>