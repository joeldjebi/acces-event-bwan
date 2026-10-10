<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistre une action dans le journal.
     */
    public static function record(Request $request, ?User $user, string $action, string $description, ?Model $subject = null): self
    {
        return self::query()->create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'action' => $action,
            'description' => Str::limit($description, 250),
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250),
        ]);
    }

    /**
     * Famille de l'action, pour la couleur de l'étiquette : create, update, delete ou auth.
     */
    public function kind(): string
    {
        return match (true) {
            str_starts_with($this->action, 'auth.') => 'auth',
            str_ends_with($this->action, '.store') => 'create',
            str_ends_with($this->action, '.destroy') => 'delete',
            default => 'update',
        };
    }
}
