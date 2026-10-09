<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TechVideo extends Model
{
    protected $fillable = [
        'label',
        'slug',
        'description',
        'tags',
        'cover_image_path',
        'youtube_url',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'position' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('label');
    }

    public function tagsInput(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Identifiant YouTube de la vidéo (null si le lien ne pointe pas vers une vidéo, ex. une chaîne).
     */
    protected function youtubeId(): Attribute
    {
        return Attribute::get(function (): ?string {
            preg_match('~(?:youtu\.be/|[?&]v=|/embed/|/shorts/)([\w-]{11})~', (string) $this->youtube_url, $matches);

            return $matches[1] ?? null;
        });
    }

    /**
     * Couverture affichée : l'image choisie en admin, sinon la miniature YouTube de la vidéo.
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_image_path
            ?: ($this->youtube_id ? 'https://i.ytimg.com/vi/'.$this->youtube_id.'/hqdefault.jpg' : null));
    }
}
