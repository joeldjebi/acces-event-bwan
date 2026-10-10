<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Once;
use InvalidArgumentException;

/**
 * Textes et images du site public modifiables dans l'administration (registre : config/site_content.php).
 * Les valeurs saisies sont stockées dans site_settings sous la clé « content.<section>.<champ> » ;
 * une valeur vide signifie « texte d'origine ».
 */
class SiteContent
{
    public const PREFIX = 'content.';

    /**
     * Définition d'un champ du registre, ou exception si la clé n'existe pas (faute de frappe dans une vue).
     *
     * @return array{label: string, default: string, type?: string, help?: string}
     */
    public static function field(string $key): array
    {
        [$section, $name] = array_pad(explode('.', $key, 2), 2, null);
        $field = config("site_content.{$section}.fields.{$name}");

        if (! is_array($field)) {
            throw new InvalidArgumentException("Contenu inconnu dans config/site_content.php : « {$key} ».");
        }

        return $field;
    }

    /**
     * Valeurs saisies dans l'administration, lues en une seule requête par page.
     *
     * @return array<string, string>
     */
    public static function overrides(): array
    {
        return once(fn () => SiteSetting::query()
            ->where('key', 'like', self::PREFIX.'%')
            ->pluck('value', 'key')
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all());
    }

    public static function override(string $key): ?string
    {
        return self::overrides()[self::PREFIX.$key] ?? null;
    }

    /**
     * Texte à afficher : valeur saisie, sinon texte d'origine. Les marqueurs « :nom » sont remplacés.
     *
     * @param  array<string, string|int>  $replace
     */
    public static function text(string $key, array $replace = []): string
    {
        $text = self::override($key) ?? self::field($key)['default'];

        foreach ($replace as $name => $value) {
            $text = str_replace(':'.$name, (string) $value, $text);
        }

        return $text;
    }

    /**
     * Adresse de l'image : image envoyée depuis l'administration, sinon image d'origine.
     */
    public static function image(string $key): string
    {
        return self::override($key) ?? asset(self::field($key)['default']);
    }

    public static function isCustom(string $key): bool
    {
        return self::override($key) !== null;
    }

    /**
     * Enregistre une valeur (null ou vide : retour au texte d'origine).
     */
    public static function put(string $key, ?string $value): void
    {
        $value = $value === null ? null : trim($value);

        if ($value === null || $value === '') {
            SiteSetting::query()->where('key', self::PREFIX.$key)->delete();
        } else {
            SiteSetting::query()->updateOrCreate(['key' => self::PREFIX.$key], ['value' => $value]);
        }

        // Les textes mémorisés pour la requête en cours sont relus à la prochaine lecture.
        Once::flush();
    }
}
