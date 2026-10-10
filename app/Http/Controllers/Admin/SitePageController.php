<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => SitePage::query()->orderBy('id')->get(),
        ]);
    }

    public function edit(SitePage $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, SitePage $page): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'heading' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);

        // Seuls les champs utilisés par cette page sont modifiés ; les autres restent intacts.
        // Ces pages forment la structure du site : elles restent toujours publiées.
        $page->update(collect($data)
            ->only(['name', 'seo_title', 'seo_description', ...array_keys($page->editableFields())])
            ->all());

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('status', 'Page mise à jour.');
    }
}
