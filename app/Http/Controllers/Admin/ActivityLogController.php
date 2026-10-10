<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Journal d'activité de l'administration, réservé au propriétaire (middleware « owner »).
 */
class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'user' => ['nullable', 'integer'],
            'kind' => ['nullable', 'in:create,update,delete,auth'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $logs = ActivityLog::query()
            ->when($filters['user'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['kind'] ?? null, fn ($query, $kind) => match ($kind) {
                'auth' => $query->where('action', 'like', 'auth.%'),
                'create' => $query->where('action', 'like', '%.store'),
                'delete' => $query->where('action', 'like', '%.destroy'),
                default => $query->where('action', 'not like', 'auth.%')
                    ->where('action', 'not like', '%.store')
                    ->where('action', 'not like', '%.destroy'),
            })
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(fn ($inner) => $inner
                ->where('description', 'like', "%{$search}%")
                ->orWhere('user_name', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity.index', [
            'logs' => $logs,
            'filters' => $filters,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
