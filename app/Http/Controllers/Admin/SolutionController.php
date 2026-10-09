<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SolutionController extends Controller
{
    public function index(): View
    {
        return view('admin.solutions.index', [
            'solutions' => Solution::query()
                ->withCount(['benefits', 'tools'])
                ->ordered()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.solutions.create', [
            'solution' => new Solution([
                'is_published' => true,
                'position' => Solution::query()->max('position') + 1,
            ]),
            'benefitsText' => '',
            'toolsText' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $solution = Solution::query()->create($data['solution']);
        $this->syncLines($solution, $data['benefits'], $data['tools']);

        return redirect()
            ->route('admin.solutions.edit', $solution)
            ->with('status', 'Solution créée avec succès.');
    }

    public function edit(Solution $solution): View
    {
        $solution->load(['benefits', 'tools']);

        return view('admin.solutions.edit', [
            'solution' => $solution,
            'benefitsText' => $solution->benefits_text,
            'toolsText' => $solution->tools_text,
        ]);
    }

    public function update(Request $request, Solution $solution): RedirectResponse
    {
        $data = $this->validatedData($request, $solution);

        $solution->update($data['solution']);
        $this->syncLines($solution, $data['benefits'], $data['tools']);

        return redirect()
            ->route('admin.solutions.edit', $solution)
            ->with('status', 'Solution mise à jour.');
    }

    public function destroy(Solution $solution): RedirectResponse
    {
        $solution->delete();

        return redirect()
            ->route('admin.solutions.index')
            ->with('status', 'Solution supprimée.');
    }

    /**
     * @return array{solution: array<string, mixed>, benefits: string, tools: string}
     */
    private function validatedData(Request $request, ?Solution $solution = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('solutions', 'slug')->ignore($solution),
            ],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'position' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'benefits' => ['nullable', 'string'],
            'tools' => ['nullable', 'string'],
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['title']);

        $imagePath = $solution?->image_path;

        if ($request->hasFile('image_file')) {
            $directory = public_path('uploads/domaines');
            File::ensureDirectoryExists($directory);

            $filename = Str::uuid().'.'.$request->file('image_file')->extension();
            $request->file('image_file')->move($directory, $filename);
            $imagePath = '/uploads/domaines/'.$filename;
        }

        return [
            'solution' => [
                'title' => $validated['title'],
                'slug' => $slug,
                'subtitle' => $validated['subtitle'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'content' => $validated['content'] ?? null,
                'image_path' => $imagePath,
                'position' => $validated['position'],
                'is_published' => $request->boolean('is_published'),
            ],
            'benefits' => $validated['benefits'] ?? '',
            'tools' => $validated['tools'] ?? '',
        ];
    }

    private function syncLines(Solution $solution, string $benefits, string $tools): void
    {
        $solution->benefits()->delete();
        $solution->tools()->delete();

        foreach ($this->parseLines($benefits) as $position => $line) {
            $solution->benefits()->create([
                'title' => $line['title'],
                'description' => $line['description'],
                'position' => $position + 1,
            ]);
        }

        foreach ($this->parseLines($tools) as $position => $line) {
            $solution->tools()->create([
                'name' => $line['title'],
                'description' => $line['description'],
                'position' => $position + 1,
            ]);
        }
    }

    /**
     * @return array<int, array{title: string, description: string|null}>
     */
    private function parseLines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->map(function (string $line) {
                [$title, $description] = array_pad(explode('|', $line, 2), 2, null);

                return [
                    'title' => trim($title),
                    'description' => $description ? trim($description) : null,
                ];
            })
            ->values()
            ->all();
    }
}
