<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Once;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'footer_tagline' => config('accessevent.brand.promise'),
            'footer_address' => (string) config('accessevent.contact.address', ''),
            'footer_phone' => (string) config('accessevent.contact.phone', ''),
            'footer_email' => (string) config('accessevent.contact.notification_email', ''),
            'footer_hours' => 'Lundi à vendredi / 09:00 - 18:00',
            'footer_whatsapp' => (string) config('accessevent.contact.phone', ''),
            'footer_linkedin_url' => '#',
            'footer_facebook_url' => '#',
            'footer_legal_text' => 'ACCÈS EVENT est une solution événementielle de BWAN TECHNOLOGIES.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function footerData(): array
    {
        return array_merge(
            self::defaults(),
            self::query()
                ->whereIn('key', array_keys(self::defaults()))
                ->pluck('value', 'key')
                ->map(fn (?string $value) => (string) $value)
                ->all(),
        );
    }

    /**
     * @param array<string, string|null> $values
     */
    public static function updateFooter(array $values): void
    {
        collect(self::defaults())
            ->keys()
            ->each(function (string $key) use ($values): void {
                self::query()->updateOrCreate([
                    'key' => $key,
                ], [
                    'value' => $values[$key] ?? null,
                ]);
            });
    }

    public static function seedDefaults(): void
    {
        Collection::make(self::defaults())
            ->each(function (string $value, string $key): void {
                self::query()->firstOrCreate([
                    'key' => $key,
                ], [
                    'value' => $value,
                ]);
            });
    }

    /**
     * Logo par défaut, livré avec le site.
     */
    public const DEFAULT_LOGO = 'images/logo/accesslogo.png';

    /**
     * Adresse du logo affiché sur le site : celui envoyé depuis l'administration, sinon le logo d'origine.
     * Mémorisé pour la requête, le logo apparaissant plusieurs fois par page.
     */
    public static function logoUrl(): string
    {
        return once(fn () => (string) (self::query()->where('key', 'site_logo')->value('value')
            ?: asset(self::DEFAULT_LOGO)));
    }

    /**
     * Chemin public du logo envoyé depuis l'administration, ou null pour le logo d'origine.
     */
    public static function uploadedLogo(): ?string
    {
        return self::query()->where('key', 'site_logo')->value('value') ?: null;
    }

    public static function updateLogo(?string $path): void
    {
        self::query()->updateOrCreate(['key' => 'site_logo'], ['value' => $path]);
        Once::flush();
    }
}
