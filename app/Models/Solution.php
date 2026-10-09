<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Solution extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'short_description',
        'content',
        'image_path',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'position' => 'integer',
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
        $query->orderBy('position')->orderBy('title');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(SolutionBenefit::class)->orderBy('position');
    }

    public function tools(): HasMany
    {
        return $this->hasMany(SolutionTool::class)->orderBy('position');
    }

    public function platformApplications(): BelongsToMany
    {
        return $this->belongsToMany(PlatformApplication::class, 'platform_application_solution')
            ->withTimestamps()
            ->orderBy('platform_applications.position')
            ->orderBy('platform_applications.name');
    }

    protected function benefitsText(): Attribute
    {
        return Attribute::get(fn () => $this->benefits
            ->map(fn (SolutionBenefit $benefit) => trim($benefit->title.' | '.$benefit->description))
            ->implode("\n"));
    }

    protected function toolsText(): Attribute
    {
        return Attribute::get(fn () => $this->tools
            ->map(fn (SolutionTool $tool) => trim($tool->name.' | '.$tool->description))
            ->implode("\n"));
    }

    /**
     * Clé de la famille du domaine (voir config accessevent.solution_groups), ou null.
     */
    protected function group(): Attribute
    {
        return Attribute::get(fn () => collect(config('accessevent.solution_groups'))
            ->search(fn (array $group) => in_array($this->slug, $group['slugs'], true)) ?: null);
    }

    protected function groupLabel(): Attribute
    {
        return Attribute::get(fn () => $this->group ? config("accessevent.solution_groups.{$this->group}.label") : null);
    }
}
