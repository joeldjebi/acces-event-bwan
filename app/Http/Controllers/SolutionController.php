<?php

namespace App\Http\Controllers;

use App\Models\PlatformApplication;
use App\Models\Solution;
use Illuminate\View\View;

class SolutionController extends Controller
{
    public function index(): View
    {
        return view('solutions.index', [
            'solutions' => Solution::query()->published()->ordered()->get(),
            'applicationCount' => PlatformApplication::query()->published()->count(),
        ]);
    }

    public function show(Solution $solution): View
    {
        abort_unless($solution->is_published, 404);

        // Domaines voisins pour la navigation « précédent / suivant ».
        $siblings = Solution::query()->published()->ordered()->get();
        $index = $siblings->search(fn (Solution $sibling) => $sibling->is($solution));

        // Suggestions : d'abord les domaines de la même famille, complétés par les suivants.
        $others = $siblings->reject(fn (Solution $sibling) => $sibling->is($solution));
        $relatedSolutions = $others->where('group', $solution->group)
            ->when($solution->group === null, fn ($related) => $related->take(0))
            ->concat($others->slice($index))
            ->concat($others)
            ->unique('id')
            ->take(3)
            ->values();

        return view('solutions.show', [
            'solution' => $solution->load([
                'benefits',
                'tools',
                'platformApplications' => fn ($query) => $query->published(),
            ]),
            'solutionCount' => $siblings->count(),
            'previousSolution' => $index > 0 ? $siblings->get($index - 1) : null,
            'nextSolution' => $siblings->get($index + 1),
            'relatedSolutions' => $relatedSolutions,
        ]);
    }
}
