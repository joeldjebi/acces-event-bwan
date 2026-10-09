<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAsset extends Model
{
    protected $fillable = [
        'name',
        'disk',
        'path',
        'url',
        'mime_type',
        'size',
        'alt_text',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function readableSize(): string
    {
        if ($this->size < 1024) {
            return $this->size.' o';
        }

        if ($this->size < 1024 * 1024) {
            return round($this->size / 1024, 1).' Ko';
        }

        return round($this->size / (1024 * 1024), 1).' Mo';
    }
}
