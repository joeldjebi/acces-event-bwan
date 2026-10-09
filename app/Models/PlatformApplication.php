<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PlatformApplication extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'role',
        'description',
        'logo_path',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
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
        $query->orderBy('position')->orderBy('name');
    }

    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(Solution::class, 'platform_application_solution')
            ->withTimestamps()
            ->orderBy('solutions.position')
            ->orderBy('solutions.title');
    }

    /**
     * Initiales des deux premiers mots du nom, ex. « ACCÈS TICKETS » → « AT ».
     */
    protected function initials(): Attribute
    {
        return Attribute::get(fn () => collect(preg_split('/[\s,&\-]+/u', (string) $this->name, -1, PREG_SPLIT_NO_EMPTY))
            ->take(2)
            ->map(fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode(''));
    }

    /**
     * Familles des domaines couverts par l'application (filtres de la page Écosystème).
     *
     * @return array<int, string>
     */
    public function groups(): array
    {
        return $this->solutions->pluck('group')->filter()->unique()->values()->all();
    }
}
