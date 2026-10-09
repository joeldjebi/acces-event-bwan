<?php

namespace App\Http\Controllers;

use App\Models\TechVideo;
use Illuminate\View\View;

class TechVideoController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.tech-videos', [
            'videos' => TechVideo::query()
                ->published()
                ->ordered()
                ->get(),
        ]);
    }
}
