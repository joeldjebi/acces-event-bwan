<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Réserve une page au propriétaire (premier administrateur), par exemple le journal d'activité.
 */
class EnsureUserIsOwner
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isOwner()) {
            abort(403);
        }

        return $next($request);
    }
}
