<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DemoRequestController as AdminDemoRequestController;
use App\Http\Controllers\Admin\FooterSettingController as AdminFooterSettingController;
use App\Http\Controllers\Admin\MediaAssetController as AdminMediaAssetController;
use App\Http\Controllers\Admin\PlatformApplicationController as AdminPlatformApplicationController;
use App\Http\Controllers\Admin\SitePageController as AdminSitePageController;
use App\Http\Controllers\Admin\SolutionController as AdminSolutionController;
use App\Http\Controllers\Admin\TechVideoController as AdminTechVideoController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\TechVideoController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;

Route::get('/', HomeController::class)->name('home');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions.index');
Route::get('/solutions/{solution}', [SolutionController::class, 'show'])->name('solutions.show');
Route::get('/ecosysteme', [PublicPageController::class, 'ecosystem'])->name('ecosystem');
Route::get('/videos-tech', TechVideoController::class)->name('tech-videos.index');
Route::get('/qui-sommes-nous', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [DemoRequestController::class, 'create'])->name('contact');
Route::post('/contact', [DemoRequestController::class, 'store'])->middleware('throttle:demo-request')->name('demo-requests.store');

// Raccourci vers phpMyAdmin : redirige vers la connexion cPanel du domaine (port 2083),
// aucun outil de base de données n'est exposé par le site lui-même.
Route::get('/{shortcut}', fn () => redirect()->away('https://'.request()->getHost().':2083'))
    ->whereIn('shortcut', ['phpmyadmin', 'phpmysql'])
    ->name('database-admin');

Route::get('/maintenance/rebuild', function () {
    $expectedToken = (string) config('accessevent.maintenance.rebuild_token');
    $givenToken = (string) request('token');

    abort_unless($expectedToken !== '' && hash_equals($expectedToken, $givenToken), 404);

    try {
        Artisan::call('optimize:clear');

        $npmBinary = (string) config('accessevent.maintenance.npm_binary', '/opt/homebrew/bin/npm');
        $process = new Process([$npmBinary, 'run', 'build'], base_path());
        $process->setEnv([
            'PATH' => '/opt/homebrew/bin:/usr/local/bin:/usr/bin:/bin',
        ]);
        $process->setTimeout(120);
        $process->run();

        return response()->json([
            'ok' => $process->isSuccessful(),
            'cache' => trim(Artisan::output()),
            'build' => trim($process->getOutput() ?: $process->getErrorOutput()),
        ], $process->isSuccessful() ? 200 : 500);
    } catch (Throwable $exception) {
        return response()->json([
            'ok' => false,
            'error' => $exception->getMessage(),
        ], 500);
    }
})->middleware('throttle:3,1')->name('maintenance.rebuild');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:admin-login')->name('admin.login.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::resource('solutions', AdminSolutionController::class)->except('show');
    Route::resource('applications', AdminPlatformApplicationController::class)->except('show');
    Route::resource('tech-videos', AdminTechVideoController::class)->except('show');
    Route::resource('pages', AdminSitePageController::class)->only(['index', 'edit', 'update']);
    Route::resource('media', AdminMediaAssetController::class)->only(['index', 'store', 'destroy']);
    Route::resource('demo-requests', AdminDemoRequestController::class)->only(['index', 'show', 'update']);
    Route::get('/settings/footer', [AdminFooterSettingController::class, 'edit'])->name('settings.footer.edit');
    Route::put('/settings/footer', [AdminFooterSettingController::class, 'update'])->name('settings.footer.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
