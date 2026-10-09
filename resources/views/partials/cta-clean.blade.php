{{-- Appel à l'action épuré : centré, sur fond clair, un seul bouton orange. --}}
<section class="cta-clean" data-reveal>
    <p class="kicker">{{ $eyebrow ?? 'Démonstration' }}</p>
    <h2>{{ $heading ?? "Vous organisez un événement\u{00A0}? Parlons-en." }}</h2>
    @isset($text)
        <p>{{ $text }}</p>
    @endisset
    <a href="{{ route('contact') }}" class="btn btn-primary">{{ $label ?? 'Demander une démonstration' }}</a>
</section>
