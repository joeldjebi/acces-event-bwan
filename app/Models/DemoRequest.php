<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    public const STATUSES = [
        'new' => 'Nouveau',
        'contacted' => 'Contacté',
        'handled' => 'Traité',
        'archived' => 'Archivé',
    ];

    protected $fillable = [
        'full_name',
        'company',
        'email',
        'phone',
        'event_type',
        'selected_solution',
        'message',
        'status',
        'internal_notes',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
