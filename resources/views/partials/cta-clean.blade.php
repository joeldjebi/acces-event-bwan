{{-- Appel à l'action épuré : centré, sur fond clair, un seul bouton orange. --}}
<section class="cta-clean" data-reveal>
    <p class="kicker">{{ $eyebrow ?? site_text('common.cta_eyebrow') }}</p>
    <h2>{{ $heading ?? site_text('common.cta_heading') }}</h2>
    @isset($text)
        <p>{{ $text }}</p>
    @endisset
    <a href="{{ route('contact') }}" class="btn btn-primary">{{ $label ?? site_text('common.cta_button') }}</a>
</section>
