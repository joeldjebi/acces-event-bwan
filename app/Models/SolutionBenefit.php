<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolutionBenefit extends Model
{
    protected $fillable = [
        'title',
        'description',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }
}
