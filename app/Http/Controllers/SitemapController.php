<?php

namespace App\Http\Controllers;

use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\Solution;
use App\Models\TechVideo;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            $this->entry(route('home'), SitePage::findByKey('home')->updated_at),
            $this->entry(route('solutions.index'), Solution::query()->max('updated_at')),
            $this->entry(route('ecosystem'), PlatformApplication::query()->max('updated_at')),
            $this->entry(route('tech-videos.index'), TechVideo::query()->max('updated_at')),
            $this->entry(route('about'), SitePage::findByKey('about')->updated_at),
            $this->entry(route('contact'), SitePage::findByKey('contact')->updated_at),
        ])
            ->merge($this->solutionEntries())
            ->filter();

        return response()
            ->view('sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string}>
     */
    private function solutionEntries(): Collection
    {
        return Solution::query()
            ->published()
            ->ordered()
            ->get(['slug', 'updated_at'])
            ->map(fn (Solution $solution) => $this->entry(route('solutions.show', $solution), $solution->updated_at));
    }

    /**
     * @param  Carbon|string|null  $lastModified
     * @return array{loc: string, lastmod: string}
     */
    private function entry(string $url, Carbon|string|null $lastModified): array
    {
        $date = $lastModified instanceof Carbon
            ? $lastModified
            : ($lastModified ? Carbon::parse($lastModified) : now());

        return [
            'loc' => $url,
            'lastmod' => $date->toAtomString(),
        ];
    }
}
