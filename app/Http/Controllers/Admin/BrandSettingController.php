<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Logo du site : envoi d'un nouveau logo ou retour au logo d'origine.
 * Formats acceptés : PNG, JPG, WebP. Le SVG est refusé, car il peut contenir du code exécuté chez les visiteurs.
 */
class BrandSettingController extends Controller
{
    private const DIRECTORY = 'uploads/brand';

    public function edit(): View
    {
        return view('admin.settings.brand', [
            'logoUrl' => SiteSetting::logoUrl(),
            'isCustom' => SiteSetting::uploadedLogo() !== null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'logo_file' => ['nullable', 'required_without:reset_logo', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:min_width=120,min_height=40'],
            'reset_logo' => ['nullable', 'boolean'],
        ], [
            'logo_file.required_without' => 'Choisissez un fichier image pour le nouveau logo.',
            'logo_file.mimes' => 'Le logo doit être au format PNG, JPG ou WebP.',
            'logo_file.dimensions' => 'Le logo doit mesurer au moins 120 × 40 pixels.',
        ]);

        $previous = SiteSetting::uploadedLogo();

        if ($request->boolean('reset_logo')) {
            SiteSetting::updateLogo(null);
            $message = "Le logo d'origine est rétabli.";
        } else {
            $directory = public_path(self::DIRECTORY);
            File::ensureDirectoryExists($directory);

            $filename = Str::uuid().'.'.$request->file('logo_file')->extension();
            $request->file('logo_file')->move($directory, $filename);

            SiteSetting::updateLogo('/'.self::DIRECTORY.'/'.$filename);
            $message = 'Le nouveau logo est en ligne.';
        }

        // L'ancien fichier envoyé n'est plus utilisé : on le supprime (jamais le logo d'origine).
        if ($previous && str_starts_with($previous, '/'.self::DIRECTORY.'/')) {
            File::delete(public_path(ltrim($previous, '/')));
        }

        return redirect()
            ->route('admin.settings.brand.edit')
            ->with('status', $message);
    }
}
