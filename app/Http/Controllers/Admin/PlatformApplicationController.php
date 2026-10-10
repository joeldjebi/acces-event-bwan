<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformApplication;
use App\Models\Solution;
use App\Support\PublicUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlatformApplicationController extends Controller
{
    public function index(): View
    {
        return view('admin.applications.index', [
            'applications' => PlatformApplication::query()
                ->withCount('solutions')
                ->ordered()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.applications.create', [
            'application' => new PlatformApplication([
                'is_published' => true,
                'position' => PlatformApplication::query()->max('position') + 1,
            ]),
            'solutions' => Solution::query()->ordered()->get(),
            'selectedSolutions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $application = PlatformApplication::query()->create($data['application']);
        $application->solutions()->sync($data['solutions']);

        return redirect()
            ->route('admin.applications.edit', $application)
            ->with('status', 'Application créée avec succès.');
    }

    public function edit(PlatformApplication $application): View
    {
        return view('admin.applications.edit', [
            'application' => $application->load('solutions'),
            'solutions' => Solution::query()->ordered()->get(),
            'selectedSolutions' => $application->solutions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, PlatformApplication $application): RedirectResponse
    {
        $data = $this->validatedData($request, $application);

        $application->update($data['application']);
        $application->solutions()->sync($data['solutions']);

        return redirect()
            ->route('admin.applications.edit', $application)
            ->with('status', 'Application mise à jour.');
    }

    public function destroy(PlatformApplication $application): RedirectResponse
    {
        $application->delete();
        PublicUpload::delete($application->logo_path);

        return redirect()
            ->route('admin.applications.index')
            ->with('status', 'Application supprimée.');
    }

    /**
     * @return array{application: array<string, mixed>, solutions: array<int, int>}
     */
    private function validatedData(Request $request, ?PlatformApplication $application = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('platform_applications', 'slug')->ignore($application),
            ],
            'role' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'logo_file' => ['nullable', 'image', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'position' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'solutions' => ['nullable', 'array'],
            'solutions.*' => ['integer', Rule::exists('solutions', 'id')],
        ]);

        $logoPath = PublicUpload::replace($request->file('logo_file'), $request->boolean('remove_logo'), $application?->logo_path, 'applications');

        return [
            'application' => [
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?: Str::slug($validated['name']),
                'role' => $validated['role'] ?? null,
                'description' => $validated['description'] ?? null,
                'logo_path' => $logoPath,
                'position' => $validated['position'],
                'is_published' => $request->boolean('is_published'),
            ],
            'solutions' => array_map('intval', $validated['solutions'] ?? []),
        ];
    }
}
