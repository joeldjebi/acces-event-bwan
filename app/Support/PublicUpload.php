<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Images envoyées depuis l'administration, stockées dans public/uploads/<dossier>.
 */
class PublicUpload
{
    /**
     * Enregistre le fichier et renvoie son chemin public, par exemple « /uploads/domaines/<uuid>.jpg ».
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        $target = public_path('uploads/'.$directory);
        File::ensureDirectoryExists($target);

        $filename = Str::uuid().'.'.$file->extension();
        $file->move($target, $filename);

        return '/uploads/'.$directory.'/'.$filename;
    }

    /**
     * Supprime un fichier envoyé. Ne touche jamais aux images livrées avec le site (hors de /uploads/).
     */
    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, '/uploads/') && ! str_contains($path, '..')) {
            File::delete(public_path(ltrim($path, '/')));
        }
    }

    /**
     * Calcule le nouveau chemin d'une image : nouveau fichier, retrait demandé, ou image inchangée.
     * L'ancien fichier est supprimé dès qu'il n'est plus utilisé.
     */
    public static function replace(?UploadedFile $file, bool $remove, ?string $current, string $directory): ?string
    {
        if ($file) {
            $path = self::store($file, $directory);
            self::delete($current);

            return $path;
        }

        if ($remove) {
            self::delete($current);

            return null;
        }

        return $current;
    }
}
