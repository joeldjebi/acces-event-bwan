<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PublicUpload;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * « Textes et images » : tous les textes et images du site public définis dans config/site_content.php.
 * Un champ vidé reprend son texte d'origine.
 */
class SiteContentController extends Controller
{
    public function index(): View
    {
        $sections = collect(config('site_content'))->map(fn (array $section, string $key) => [
            'key' => $key,
            'label' => $section['label'],
            'description' => $section['description'],
            'total' => count($section['fields']),
            'custom' => collect($section['fields'])->keys()
                ->filter(fn (string $field) => SiteContent::isCustom("{$key}.{$field}"))
                ->count(),
        ]);

        return view('admin.contents.index', ['sections' => $sections]);
    }

    public function edit(string $section): View
    {
        return view('admin.contents.edit', [
            'sectionKey' => $section,
            'section' => $this->section($section),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        $definition = $this->section($section);
        $rules = [];

        foreach ($definition['fields'] as $name => $field) {
            $rules[$name] = match ($field['type'] ?? 'text') {
                'image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
                'text' => ['nullable', 'string', 'max:500'],
                default => ['nullable', 'string', 'max:5000'],
            };

            if (($field['type'] ?? 'text') === 'image') {
                $rules["remove_{$name}"] = ['nullable', 'boolean'];
            }
        }

        $request->validate($rules, [
            '*.mimes' => "L'image doit être au format PNG, JPG ou WebP.",
        ]);

        foreach ($definition['fields'] as $name => $field) {
            $key = "{$section}.{$name}";

            if (($field['type'] ?? 'text') === 'image') {
                $current = SiteContent::override($key);
                $path = PublicUpload::replace($request->file($name), $request->boolean("remove_{$name}"), $current, 'site');

                if ($path !== $current) {
                    SiteContent::put($key, $path);
                }

                continue;
            }

            $value = $request->input($name);

            // Saisir exactement le texte d'origine revient à ne rien personnaliser.
            SiteContent::put($key, $value === $field['default'] ? null : $value);
        }

        return redirect()
            ->route('admin.contents.edit', $section)
            ->with('status', "Les textes « {$definition['label']} » sont enregistrés.");
    }

    /**
     * @return array{label: string, description: string, fields: array<string, array<string, string>>}
     */
    private function section(string $section): array
    {
        $definition = config("site_content.{$section}");
        abort_unless(is_array($definition), 404);

        return $definition;
    }
}
