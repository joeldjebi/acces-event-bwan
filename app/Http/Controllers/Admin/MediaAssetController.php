<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaAssetController extends Controller
{
    public function index(): View
    {
        return view('admin.media.index', [
            'mediaAssets' => MediaAsset::query()
                ->with('uploader')
                ->latest()
                ->paginate(18),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'name' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $data['file'];
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        $directory = public_path('uploads/media');
        File::ensureDirectoryExists($directory);

        $filename = Str::uuid().'.'.$file->extension();
        $file->move($directory, $filename);
        $path = 'uploads/media/'.$filename;

        MediaAsset::query()->create([
            'name' => $data['name'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'disk' => 'public',
            'path' => $path,
            'url' => '/'.$path,
            'mime_type' => $mimeType,
            'size' => $size,
            'alt_text' => $data['alt_text'] ?? null,
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Média ajouté avec succès.');
    }

    public function destroy(MediaAsset $medium): RedirectResponse
    {
        $path = public_path($medium->path);

        if (File::exists($path)) {
            File::delete($path);
        }

        $medium->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Média supprimé.');
    }
}
