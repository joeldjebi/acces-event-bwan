<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechVideo;
use App\Support\PublicUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TechVideoController extends Controller
{
    public function index(): View
    {
        return view('admin.tech-videos.index', [
            'videos' => TechVideo::query()
                ->ordered()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.tech-videos.create', [
            'video' => new TechVideo([
                'is_published' => true,
                'position' => TechVideo::query()->max('position') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $video = TechVideo::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.tech-videos.edit', $video)
            ->with('status', 'Vidéo technique créée avec succès.');
    }

    public function edit(TechVideo $techVideo): View
    {
        return view('admin.tech-videos.edit', [
            'video' => $techVideo,
        ]);
    }

    public function update(Request $request, TechVideo $techVideo): RedirectResponse
    {
        $techVideo->update($this->validatedData($request, $techVideo));

        return redirect()
            ->route('admin.tech-videos.edit', $techVideo)
            ->with('status', 'Vidéo technique mise à jour.');
    }

    public function destroy(TechVideo $techVideo): RedirectResponse
    {
        $techVideo->delete();
        PublicUpload::delete($techVideo->cover_image_path);

        return redirect()
            ->route('admin.tech-videos.index')
            ->with('status', 'Vidéo technique supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?TechVideo $video = null): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tech_videos', 'slug')->ignore($video),
            ],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'cover_image_file' => ['nullable', 'image', 'max:5120'],
            'remove_cover' => ['nullable', 'boolean'],
            'youtube_url' => ['required', 'url', 'max:255'],
            'position' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $coverImagePath = PublicUpload::replace($request->file('cover_image_file'), $request->boolean('remove_cover'), $video?->cover_image_path, 'videos-tech');

        return [
            'label' => $validated['label'],
            'slug' => $validated['slug'] ?: Str::slug($validated['label']),
            'description' => $validated['description'] ?? null,
            'tags' => $this->parseTags($validated['tags'] ?? ''),
            'cover_image_path' => $coverImagePath,
            'youtube_url' => $validated['youtube_url'],
            'position' => $validated['position'],
            'is_published' => $request->boolean('is_published'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function parseTags(string $tags): array
    {
        return collect(explode(',', $tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
