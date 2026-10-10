<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journalise chaque action de l'administration qui modifie des données (POST, PUT, PATCH, DELETE).
 * Les consultations ne sont pas enregistrées, ni les envois refusés par la validation.
 */
class LogAdminActivity
{
    /**
     * Libellé de l'action, par nom de route. Une route absente de la liste est journalisée avec son nom.
     *
     * @var array<string, string>
     */
    private const ACTIONS = [
        'admin.solutions.store' => 'a créé le domaine',
        'admin.solutions.update' => 'a modifié le domaine',
        'admin.solutions.destroy' => 'a supprimé le domaine',
        'admin.applications.store' => "a créé l'application",
        'admin.applications.update' => "a modifié l'application",
        'admin.applications.destroy' => "a supprimé l'application",
        'admin.tech-videos.store' => 'a créé la vidéo',
        'admin.tech-videos.update' => 'a modifié la vidéo',
        'admin.tech-videos.destroy' => 'a supprimé la vidéo',
        'admin.pages.update' => 'a modifié la page',
        'admin.media.store' => 'a ajouté un média',
        'admin.media.destroy' => 'a supprimé le média',
        'admin.demo-requests.update' => 'a mis à jour la demande de',
        'admin.settings.footer.update' => 'a modifié le pied de page',
        'admin.settings.brand.update' => 'a modifié le logo du site',
        'admin.contents.update' => 'a modifié les textes et images de la section',
        'admin.users.store' => "a créé l'administrateur",
        'admin.users.update' => "a modifié l'administrateur",
        'admin.users.destroy' => "a supprimé l'administrateur",
        'admin.logout' => "s'est déconnecté",
    ];

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lu avant la requête : après une déconnexion, l'utilisateur n'est plus disponible.
        $user = $request->user();
        $subject = $this->subject($request);
        $subjectLabel = $this->subjectLabel($request, $subject);

        $response = $next($request);

        if (! $request->isMethodSafe() && $user && $response->getStatusCode() < 400 && ! $this->failedValidation($request)) {
            $route = (string) $request->route()?->getName();
            $description = trim((self::ACTIONS[$route] ?? $route).' '.$subjectLabel);

            ActivityLog::record($request, $user, $route, $description, $subject);
        }

        return $response;
    }

    private function subject(Request $request): ?Model
    {
        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if ($parameter instanceof Model) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Nom lisible de l'élément concerné : celui du modèle existant, sinon celui saisi dans le formulaire.
     */
    private function subjectLabel(Request $request, ?Model $subject): string
    {
        foreach (['title', 'name', 'label', 'full_name', 'email'] as $attribute) {
            $value = $subject?->getAttribute($attribute) ?? $request->input($attribute);

            if (is_string($value) && $value !== '') {
                return '« '.$value.' »';
            }
        }

        // Section de « Textes et images » (paramètre de route, pas un modèle).
        $section = $request->route('section');
        if (is_string($section) && is_string(config("site_content.{$section}.label"))) {
            return '« '.config("site_content.{$section}.label").' »';
        }

        return $subject ? '#'.$subject->getKey() : '';
    }

    private function failedValidation(Request $request): bool
    {
        return $request->hasSession() && $request->session()->has('errors');
    }
}
