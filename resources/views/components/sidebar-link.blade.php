@props(['active' => false, 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'group flex items-center px-3.5 py-2.5 bg-slate-800/90 text-white font-bold rounded-xl border-l-4 border-theme-primary shadow-xs transition duration-150 text-xs'
            : 'group flex items-center px-3.5 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800/60 rounded-xl font-medium transition duration-150 text-xs';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} mr-3 w-5 text-center text-sm {{ ($active ?? false) ? 'text-theme-primary' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
