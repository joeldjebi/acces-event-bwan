@props(['solution', 'eager' => false])

{{-- Image d'un domaine : la photo ajoutée dans l'admin, sinon un visuel aux couleurs de la marque
     (pictogramme du domaine sur un dégradé propre à sa famille). --}}
@if ($solution->image_path)
    <img src="{{ $solution->image_path }}" alt="{{ $solution->title }}" {{ $attributes->class('media-cover') }} @unless ($eager) loading="lazy" @endunless>
@else
    <div {{ $attributes->class(['media-cover', 'solution-visual', 'is-'.($solution->group ?? 'default')]) }} aria-hidden="true">
        <x-solution-icon :slug="$solution->slug" />
        <span>{{ str_pad((string) $solution->position, 2, '0', STR_PAD_LEFT) }}</span>
    </div>
@endif
