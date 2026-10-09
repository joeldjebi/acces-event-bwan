@props(['name'])

{{-- Pictogrammes au trait utilisés dans les pages publiques (24 × 24, couleur héritée). --}}
<svg {{ $attributes->class('icon') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="M3.5 6.5l8.5 6 8.5-6" />
            @break
        @case('phone')
            <path d="M5 4h3l1.5 4.5-2 1.2a11 11 0 0 0 6.8 6.8l1.2-2L20 16v3a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2z" />
            @break
        @case('chat')
            <path d="M4 19.5l1.2-3.6A8 8 0 1 1 8.4 19z" />
            <path d="M9 11h.01M12 11h.01M15 11h.01" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
            @break
        @case('pin')
            <path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z" />
            <circle cx="12" cy="9.5" r="2.5" />
            @break
        @case('sync')
            <path d="M20 11a8 8 0 0 0-14.3-4.9L4 8" />
            <path d="M4 4v4h4" />
            <path d="M4 13a8 8 0 0 0 14.3 4.9L20 16" />
            <path d="M20 20v-4h-4" />
            @break
        @case('bolt')
            <path d="M13 3L5 13.5h6L10 21l8-10.5h-6z" />
            @break
        @case('layers')
            <path d="M12 3l9 5-9 5-9-5z" />
            <path d="M3 13l9 5 9-5" />
            @break
        @case('globe')
            <circle cx="12" cy="12" r="9" />
            <path d="M3 12h18M12 3c2.5 2.5 3.8 5.5 3.8 9s-1.3 6.5-3.8 9c-2.5-2.5-3.8-5.5-3.8-9S9.5 5.5 12 3z" />
            @break
        @case('shield')
            <path d="M12 3l8 3v6c0 5-3.3 8.5-8 9.8C7.3 20.5 4 17 4 12V6z" />
            <path d="M8.5 12l2.5 2.5 4.5-5" />
            @break
        @case('play')
            <circle cx="12" cy="12" r="9" />
            <path d="M10 8.5v7l5.5-3.5z" />
            @break
        @case('plug')
            <path d="M9 3v5M15 3v5" />
            <path d="M6 8h12v3a6 6 0 0 1-12 0z" />
            <path d="M12 17v4" />
            @break
        @case('users')
            <circle cx="9" cy="8" r="3.5" />
            <path d="M2.5 20c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5" />
            <path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 13.8c2.1.8 3.5 3 3.5 6.2" />
            @break
        @case('check')
            <circle cx="12" cy="12" r="9" />
            <path d="M8 12.5l2.7 2.7L16 9.8" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
