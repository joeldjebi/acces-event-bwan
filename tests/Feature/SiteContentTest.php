<?php

use App\Models\ActivityLog;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::factory()->create(['role' => 'admin']);
});

it('déclare dans le registre chaque texte et image utilisés par le site', function () {
    $files = collect(File::allFiles(resource_path('views')))->merge(File::allFiles(app_path()));
    $missing = [];

    foreach ($files as $file) {
        preg_match_all("/site_(?:text|image)\\(\\s*'([a-z_]+\\.[a-z0-9_]+)'/", $file->getContents(), $matches);

        foreach ($matches[1] as $key) {
            if (! is_array(config('site_content.'.str_replace('.', '.fields.', $key)))) {
                $missing[] = $key.' ('.$file->getRelativePathname().')';
            }
        }
    }

    // Clés construites dynamiquement : familles de domaines et formats d'événements.
    foreach (array_keys(config('accessevent.solution_groups')) as $group) {
        config("site_content.families.fields.{$group}") ?? $missing[] = "families.{$group}";
    }
    foreach (array_keys(config('accessevent.event_types')) as $type) {
        config("site_content.formats.fields.{$type}_label") ?? $missing[] = "formats.{$type}_label";
        config("site_content.formats.fields.{$type}_text") ?? $missing[] = "formats.{$type}_text";
    }

    expect($missing)->toBe([]);
});

it('affiche chaque section de « Textes et images » dans l\'administration', function () {
    $this->actingAs($this->admin)->get('/admin/contents')->assertOk()->assertSee('Textes et images');

    foreach (array_keys(config('site_content')) as $section) {
        $this->actingAs($this->admin)->get("/admin/contents/{$section}/edit")->assertOk();
    }

    $this->actingAs($this->admin)->get('/admin/contents/inconnue/edit')->assertNotFound();
});

it('met à jour le site public, puis revient au texte d\'origine quand le champ est vidé', function () {
    $this->get('/')->assertSee('Une opération fluide, du paramétrage au reporting.');

    $this->actingAs($this->admin)
        ->put('/admin/contents/home', ['method_heading' => 'Titre méthode personnalisé', 'domains_link' => 'Les :count domaines en détail'])
        ->assertRedirect('/admin/contents/home/edit');

    $this->get('/')
        ->assertSee('Titre méthode personnalisé')
        ->assertDontSee('Une opération fluide, du paramétrage au reporting.')
        ->assertSee('Les 12 domaines en détail');

    expect(ActivityLog::query()->where('action', 'admin.contents.update')->value('description'))
        ->toBe('a modifié les textes et images de la section « Accueil »');

    // Champ vidé : retour au texte d'origine, plus rien n'est stocké
    $this->actingAs($this->admin)->put('/admin/contents/home', ['method_heading' => ''])->assertSessionHasNoErrors();
    $this->get('/')->assertSee('Une opération fluide, du paramétrage au reporting.');
    expect(SiteSetting::query()->where('key', 'content.home.method_heading')->exists())->toBeFalse();
});

it('ne stocke rien quand on saisit exactement le texte d\'origine', function () {
    $this->actingAs($this->admin)->put('/admin/contents/common', ['header_cta' => 'Démonstration'])->assertSessionHasNoErrors();

    expect(SiteContent::isCustom('common.header_cta'))->toBeFalse();
});

it('modifie les textes communs, les familles et les formats sur toutes les pages concernées', function () {
    $this->actingAs($this->admin)->put('/admin/contents/common', ['nav_videos' => 'Tutoriels', 'footer_rights' => 'Droits réservés test.'])->assertSessionHasNoErrors();
    $this->actingAs($this->admin)->put('/admin/contents/families', ['vendre' => 'Billetterie & ventes'])->assertSessionHasNoErrors();
    $this->actingAs($this->admin)->put('/admin/contents/formats', ['concerts_label' => 'Grands concerts'])->assertSessionHasNoErrors();

    foreach (['/', '/solutions', '/contact'] as $url) {
        $this->get($url)->assertSee('Tutoriels')->assertSee('Droits réservés test.');
    }

    $this->get('/solutions')->assertSee('Billetterie &amp; ventes', false);
    $this->get('/solutions/billetterie-360')->assertSee('Billetterie &amp; ventes', false);
    $this->get('/')->assertSee('Grands concerts');
});

it('remplace la photo de l\'accueil, masque les points cliquables, puis rétablit l\'originale', function () {
    $this->get('/')->assertSee('class="hotspot-dot"', false);

    $this->actingAs($this->admin)
        ->put('/admin/contents/home', ['scene_image' => UploadedFile::fake()->image('scene.jpg', 1600, 1000)])
        ->assertSessionHasNoErrors();

    $path = SiteContent::override('home.scene_image');
    expect($path)->toStartWith('/uploads/site/')->and(file_exists(public_path(ltrim($path, '/'))))->toBeTrue();
    $this->get('/')->assertSee($path, false)->assertDontSee('class="hotspot-dot"', false);

    $this->actingAs($this->admin)->put('/admin/contents/home', ['remove_scene_image' => 1])->assertSessionHasNoErrors();

    expect(SiteContent::override('home.scene_image'))->toBeNull()
        ->and(file_exists(public_path(ltrim($path, '/'))))->toBeFalse();
    $this->get('/')->assertSee('images/hero/acces-event-hero.png', false)->assertSee('class="hotspot-dot"', false);
});

it('refuse une image SVG', function () {
    $this->actingAs($this->admin)
        ->put('/admin/contents/about', ['image' => UploadedFile::fake()->create('photo.svg', 5, 'image/svg+xml')])
        ->assertSessionHasErrors('image');
});
