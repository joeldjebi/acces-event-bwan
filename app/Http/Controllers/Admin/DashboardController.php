<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use App\Models\MediaAsset;
use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\Solution;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $period = CarbonPeriod::create(now()->subDays(6)->startOfDay(), now()->startOfDay());

        $requestsByDay = DemoRequest::query()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn (DemoRequest $request) => $request->created_at->format('Y-m-d'));

        $requestChart = collect($period)
            ->map(function (Carbon $date) use ($requestsByDay): array {
                $key = $date->format('Y-m-d');

                return [
                    'label' => $date->isoFormat('dd D'),
                    'value' => $requestsByDay->get($key, collect())->count(),
                ];
            })
            ->values();

        $maxChartValue = max(1, $requestChart->max('value'));

        return view('admin.dashboard', [
            'stats' => [
                'solutions' => Solution::query()->count(),
                'published_solutions' => Solution::query()->where('is_published', true)->count(),
                'applications' => PlatformApplication::query()->count(),
                'demo_requests' => DemoRequest::query()->count(),
                'new_requests' => DemoRequest::query()->where('status', 'new')->count(),
                'handled_requests' => DemoRequest::query()->where('status', 'handled')->count(),
                'pages' => SitePage::query()->count(),
                'media' => MediaAsset::query()->count(),
            ],
            'requestChart' => $requestChart,
            'maxChartValue' => $maxChartValue,
            'recentRequests' => DemoRequest::query()->latest()->take(5)->get(),
        ]);
    }
}
