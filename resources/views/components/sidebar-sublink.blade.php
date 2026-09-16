@props(['active' => false, 'href' => '#'])

@php
$classes = $active
    ? 'text-cyan-300 font-semibold border-cyan-400 bg-indigo-950/40'
    : 'text-slate-400 hover:text-slate-100 hover:border-slate-600 font-normal';
@endphp

<a href="{{ $href }}" class="flex items-center gap-2 pl-3 py-1.5 text-xs border-l-2 transition-all rounded-r-lg {{ $classes }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-cyan-400 shadow-sm shadow-cyan-400' : 'bg-slate-700' }}"></span>
    <span class="truncate">{{ $slot }}</span>
</a>
