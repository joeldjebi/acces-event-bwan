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
