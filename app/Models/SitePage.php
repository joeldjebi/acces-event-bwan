<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePage extends Model
{
    protected $fillable = [
        'key',
        'name',
        'eyebrow',
        'heading',
        'summary',
        'body',
        'cta_label',
        'cta_url',
        'seo_title',
        'seo_description',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /**
     * Champs affichés sur le site pour chaque page, avec l'endroit où ils apparaissent.
     * Les champs absents ne sont pas utilisés par la page et ne sont pas proposés dans l'administration.
     *
     * @var array<string, array<string, string>>
     */
    public const FIELDS = [
        'home' => [
            'eyebrow' => 'Sur-titre, au-dessus du grand titre',
            'heading' => "Grand titre de l'accueil",
            'summary' => 'Texte sous le grand titre',
            'cta_label' => 'Texte du bouton principal',
            'cta_url' => 'Lien du bouton principal (vide : page Contact)',
            'body' => 'Grand texte animé, sous la photo (les mots s\'illuminent au défilement)',
        ],
        'ecosystem' => [
            'eyebrow' => 'Sur-titre, au-dessus du grand titre',
            'heading' => 'Grand titre de la page',
            'summary' => 'Texte sous le grand titre',
            'cta_label' => 'Titre du bandeau bleu « Interopérabilité »',
            'body' => 'Texte du bandeau bleu « Interopérabilité »',
        ],
        'about' => [
            'eyebrow' => 'Sur-titre, au-dessus du grand titre',
            'heading' => 'Grand titre de la page',
            'summary' => 'Texte sous le grand titre',
            'body' => 'Notre histoire : le 1er paragraphe s\'affiche en grand, les suivants à côté',
        ],
        'contact' => [
            'eyebrow' => 'Sur-titre, au-dessus du grand titre',
            'heading' => 'Grand titre de la page',
            'summary' => 'Texte sous le grand titre',
            'cta_label' => "Texte du bouton d'envoi du formulaire",
        ],
    ];

    /**
     * Champs de contenu de cette page (les champs de référencement sont communs à toutes).
     *
     * @return array<string, string>
     */
    public function editableFields(): array
    {
        return self::FIELDS[$this->key] ?? [
            'eyebrow' => 'Sur-titre',
            'heading' => 'Titre principal',
            'summary' => 'Résumé',
            'body' => 'Corps de page',
            'cta_label' => "Libellé de l'appel à l'action",
            'cta_url' => "URL de l'appel à l'action",
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    public static function findByKey(string $key): self
    {
        return self::query()->where('key', $key)->firstOrFail();
    }

    /**
     * @return array<int, string>
     */
    public function bodyParagraphs(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->body) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->values()
            ->all();
    }
}
