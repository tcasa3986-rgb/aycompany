@props(['name' => 'home'])

@php
    $svg = match ($name) {
        'home'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10"/>',
        'box'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 8V6a2 2 0 00-2-2H5a2 2 0 00-2 2v2m18 0l-9 6-9-6m18 0v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8"/>',
        'cart'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>',
        'users'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-3.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        'chart'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 4 5-7"/>',
        'cog'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.591 1.07c1.546-.94 3.31.823 2.37 2.37a1.724 1.724 0 001.07 2.591c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.07 2.591c.94 1.546-.824 3.31-2.37 2.37a1.724 1.724 0 00-2.591 1.07c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.591-1.07c-1.546.94-3.31-.823-2.37-2.37a1.724 1.724 0 00-1.07-2.591c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.07-2.591c-.94-1.546.823-3.31 2.37-2.37.996.608 2.296.07 2.591-1.07z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'grid'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5h6v6H4V5zm10 0h6v6h-6V5zM4 13h6v6H4v-6zm10 0h6v6h-6v-6z"/>',
        'search'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>',
        'logout'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
        'plus'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>',
        'pencil'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        'trash'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>',
        'eye'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
        default     => '<circle cx="12" cy="12" r="9"/>',
    };
@endphp

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    {!! $svg !!}
</svg>
