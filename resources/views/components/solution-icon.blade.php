@props(['slug' => null])

{{-- Pictogramme au trait d'un domaine, choisi d'après son slug (icône générique sinon). --}}
<svg {{ $attributes->class('solution-icon') }} viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($slug)
        @case('billetterie-360')
            {{-- Billet avec encoches et pointillés --}}
            <path d="M6 15a3 3 0 0 1 3-3h30a3 3 0 0 1 3 3v4a5 5 0 0 0 0 10v4a3 3 0 0 1-3 3H9a3 3 0 0 1-3-3v-4a5 5 0 0 0 0-10z" />
            <path d="M30 13v3m0 6v4m0 6v3" />
            <path d="M13 20h10m-10 8h7" />
            @break
        @case('controle-acces')
            {{-- QR code dans un cadre de scan --}}
            <path d="M6 14V9a3 3 0 0 1 3-3h5m20 0h5a3 3 0 0 1 3 3v5m0 20v5a3 3 0 0 1-3 3h-5m-20 0H9a3 3 0 0 1-3-3v-5" />
            <rect x="14" y="14" width="8" height="8" rx="1" />
            <rect x="26" y="14" width="8" height="8" rx="1" />
            <rect x="14" y="26" width="8" height="8" rx="1" />
            <path d="M26 26h3v3m5-3v8h-8v-3" />
            @break
        @case('accreditation')
            {{-- Badge porté au cordon --}}
            <path d="M18 4h12M24 4v8" />
            <rect x="10" y="12" width="28" height="32" rx="3" />
            <circle cx="24" cy="24" r="5" />
            <path d="M16 38c1.5-4 4.5-6 8-6s6.5 2 8 6" />
            @break
        @case('marketing-crm')
            {{-- Mégaphone --}}
            <path d="M8 20v8a2 2 0 0 0 2 2h4l14 8V10l-14 8h-4a2 2 0 0 0-2 2z" />
            <path d="M14 30l3 10h5l-2-9" />
            <path d="M35 17a8 8 0 0 1 0 14m4-18a14 14 0 0 1 0 22" />
            @break
        @case('baobaa')
            {{-- Réseau de partenaires --}}
            <circle cx="24" cy="10" r="5" />
            <circle cx="10" cy="36" r="5" />
            <circle cx="38" cy="36" r="5" />
            <path d="M21 14.5L13 31.5m14-17l8 17M15 36h18" />
            @break
        @case('paiements-digitaux')
            {{-- Portefeuille --}}
            <path d="M38 16V11a3 3 0 0 0-3-3H10a4 4 0 0 0 0 8h28a3 3 0 0 1 3 3v19a3 3 0 0 1-3 3H10a4 4 0 0 1-4-4V12" />
            <path d="M41 24h-7a4 4 0 0 0 0 8h7z" />
            <circle cx="34" cy="28" r="0.5" fill="currentColor" />
            @break
        @case('pass-parking')
            {{-- Panneau P --}}
            <rect x="8" y="6" width="32" height="36" rx="6" />
            <path d="M19 34V14h7a6 6 0 0 1 0 12h-7" />
            @break
        @case('securite-surete-event')
            {{-- Bouclier validé --}}
            <path d="M24 4l16 6v12c0 10-6.5 18-16 22C14.5 40 8 32 8 22V10z" />
            <path d="M17 24l5 5 9-10" />
            @break
        @case('signaletique-evenementielle')
            {{-- Panneau directionnel --}}
            <path d="M24 4v40m-8 0h16" />
            <path d="M10 10h22l5 5-5 5H10z" />
            <path d="M38 24H16l-5 5 5 5h22z" />
            @break
        @case('accessoiriste-evenement')
            {{-- Bracelet événementiel --}}
            <ellipse cx="24" cy="24" rx="18" ry="10" />
            <path d="M6 24v4c0 5.5 8 10 18 10s18-4.5 18-10v-4" />
            <rect x="19" y="10" width="10" height="7" rx="1.5" />
            @break
        @case('gestion-benevoles')
            {{-- Équipe --}}
            <circle cx="18" cy="16" r="6" />
            <path d="M6 40c0-7 5.5-12 12-12s12 5 12 12" />
            <circle cx="34" cy="18" r="5" />
            <path d="M32 28c6 0 10 4.5 10 11" />
            @break
        @case('tourisme')
            {{-- Repère sur une carte --}}
            <path d="M6 12l11-5 14 5 11-5v29l-11 5-14-5-11 5z" />
            <path d="M17 7v29m14-24v29" />
            @break
        @default
            {{-- Module générique --}}
            <rect x="8" y="8" width="13" height="13" rx="3" />
            <rect x="27" y="8" width="13" height="13" rx="3" />
            <rect x="8" y="27" width="13" height="13" rx="3" />
            <path d="M33.5 27v13M27 33.5h13" />
    @endswitch
</svg>
