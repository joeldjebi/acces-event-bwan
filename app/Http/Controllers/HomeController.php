<?php

namespace App\Http\Controllers;

use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\Solution;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $solutions = Solution::query()->published()->ordered()->get();

        return view('welcome', [
            'page' => SitePage::findByKey('home'),
            'solutions' => $solutions,
            'solutionCount' => $solutions->count(),
            // Formats d'événements avec leurs domaines recommandés (ceux qui sont publiés).
            'eventTypes' => collect(config('accessevent.event_types'))
                ->map(fn (array $type) => $type + [
                    'solutions' => $solutions->whereIn('slug', $type['slugs'])
                        ->sortBy(fn ($solution) => array_search($solution->slug, $type['slugs'], true))
                        ->values(),
                ])
                ->filter(fn (array $type) => $type['solutions']->isNotEmpty()),
            'applications' => PlatformApplication::query()->published()->ordered()->get(['name', 'role']),
        ]);
    }
}
