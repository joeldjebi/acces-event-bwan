<?php

namespace App\Http\Controllers;

use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\Solution;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function ecosystem(): View
    {
        return view('pages.ecosystem', [
            'page' => SitePage::findByKey('ecosystem'),
            'applications' => PlatformApplication::query()
                ->with('solutions')
                ->published()
                ->ordered()
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'page' => SitePage::findByKey('about'),
            'solutionCount' => Solution::query()->published()->count(),
            'applicationCount' => PlatformApplication::query()->published()->count(),
        ]);
    }
}
