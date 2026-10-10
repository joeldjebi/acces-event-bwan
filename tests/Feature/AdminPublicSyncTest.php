<?php

/*
 * Vérifie, module par module, que chaque action de l'administration (création, modification,
 * dépublication, suppression) se répercute correctement sur le site public.
 */

use App\Models\DemoRequest;
use App\Models\MediaAsset;
use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\Solution;
use App\Models\TechVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->startedAt = time();
});

// Supprime les images envoyées pendant le test (elles sont écrites dans public/uploads).
afterEach(function () {
    foreach (glob(public_path('uploads/*/*')) ?: [] as $file) {
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-/', basename($file)) && filemtime($file) >= $this->startedAt - 1) {
            @unlink($file);
        }
    }
});

function solutionPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Domaine de test',
        'slug' => 'domaine-de-test',
        'subtitle' => 'Accroche du domaine de test',
        'short_description' => 'Description courte du domaine de test.',
        'content' => "Premier paragraphe.\nSecond paragraphe.",
        'position' => 99,
        'is_published' => 1,
        'benefits' => "Bénéfice alpha | Détail alpha\nBénéfice bêta | Détail bêta",
        'tools' => 'Outil gamma | Détail gamma',
    ], $overrides);
}

it('solutions : création, modification, dépublication, retrait d\'image et suppression', function () {
    $this->actingAs($this->admin)
        ->post('/admin/solutions', solutionPayload(['image_file' => UploadedFile::fake()->image('photo.jpg', 800, 600)]))
        ->assertSessionHasNoErrors();

    $solution = Solution::query()->where('slug', 'domaine-de-test')->firstOrFail();
    $image = $solution->image_path;

    expect($image)->toStartWith('/uploads/domaines/')
        ->and(file_exists(public_path(ltrim($image, '/'))))->toBeTrue();

    // Visible partout sur le site
    $this->get('/')->assertSee('Domaine de test');
    $this->get('/solutions')->assertSee('Domaine de test')->assertSee($image, false);
    $this->get('/solutions/domaine-de-test')->assertOk()
        ->assertSee('Accroche du domaine de test')
        ->assertSee('Second paragraphe.')
        ->assertSee('Bénéfice alpha')->assertSee('Détail bêta')
        ->assertSee('Outil gamma');
    $this->get('/sitemap.xml')->assertSee('/solutions/domaine-de-test', false);

    // Modification
    $this->actingAs($this->admin)
        ->put("/admin/solutions/{$solution->slug}", solutionPayload(['title' => 'Domaine renommé', 'benefits' => 'Nouveau bénéfice | Nouveau détail']))
        ->assertSessionHasNoErrors();

    $this->get('/solutions/domaine-de-test')->assertSee('Domaine renommé')->assertSee('Nouveau bénéfice')->assertDontSee('Bénéfice alpha');
    expect($solution->fresh()->image_path)->toBe($image);

    // Retrait de l'image : fichier supprimé, pictogramme affiché
    $this->actingAs($this->admin)
        ->put("/admin/solutions/{$solution->slug}", solutionPayload(['title' => 'Domaine renommé', 'remove_image' => 1]))
        ->assertSessionHasNoErrors();

    expect($solution->fresh()->image_path)->toBeNull()
        ->and(file_exists(public_path(ltrim($image, '/'))))->toBeFalse();
    $this->get('/solutions/domaine-de-test')->assertDontSee($image, false)->assertSee('solution-visual', false);

    // Dépublication : absent du site, fiche en 404
    $this->actingAs($this->admin)
        ->put("/admin/solutions/{$solution->slug}", solutionPayload(['title' => 'Domaine renommé', 'is_published' => 0]))
        ->assertSessionHasNoErrors();

    $this->get('/')->assertDontSee('Domaine renommé');
    $this->get('/solutions')->assertDontSee('Domaine renommé');
    $this->get('/solutions/domaine-de-test')->assertNotFound();
    $this->get('/sitemap.xml')->assertDontSee('/solutions/domaine-de-test', false);

    // Suppression
    $this->actingAs($this->admin)->delete("/admin/solutions/{$solution->slug}")->assertRedirect('/admin/solutions');
    expect(Solution::query()->where('slug', 'domaine-de-test')->exists())->toBeFalse();
});

it('solutions : le nombre de domaines suit les publications', function () {
    $published = Solution::query()->published()->count();

    $this->get('/solutions')->assertSee("Les {$published} domaines", false);

    Solution::query()->published()->first()->update(['is_published' => false]);
    $this->get('/solutions')->assertSee('Les '.($published - 1).' domaines', false);
});

it('applications : affichage sur l\'écosystème, l\'accueil et la fiche domaine', function () {
    $visible = Solution::query()->published()->firstOrFail();
    $hidden = Solution::query()->published()->skip(1)->firstOrFail();
    $hidden->update(['is_published' => false]);

    $this->actingAs($this->admin)
        ->post('/admin/applications', [
            'name' => 'APPLI DE TEST',
            'slug' => 'appli-de-test',
            'role' => 'Rôle de l\'appli de test.',
            'position' => 99,
            'is_published' => 1,
            'solutions' => [$visible->id, $hidden->id],
            'logo_file' => UploadedFile::fake()->image('logo.png', 300, 300),
        ])
        ->assertSessionHasNoErrors();

    $application = PlatformApplication::query()->where('slug', 'appli-de-test')->firstOrFail();

    $this->get('/ecosysteme')
        ->assertSee('APPLI DE TEST')->assertSee("Rôle de l&#039;appli de test.", false)
        ->assertSee($application->logo_path, false)
        // Le domaine dépublié n'apparaît pas dans les étiquettes de l'application
        ->assertSee(route('solutions.show', $visible), false)
        ->assertDontSee(route('solutions.show', $hidden), false);
    $this->get('/')->assertSee('APPLI DE TEST');
    $this->get("/solutions/{$visible->slug}")->assertSee('APPLI DE TEST');

    // Retrait du logo : les initiales s'affichent
    $logo = $application->logo_path;
    $this->actingAs($this->admin)
        ->put("/admin/applications/{$application->slug}", ['name' => 'APPLI DE TEST', 'slug' => 'appli-de-test', 'position' => 99, 'is_published' => 1, 'solutions' => [$visible->id], 'remove_logo' => 1])
        ->assertSessionHasNoErrors();
    expect($application->fresh()->logo_path)->toBeNull()
        ->and(file_exists(public_path(ltrim($logo, '/'))))->toBeFalse();
    $this->get('/ecosysteme')->assertSee('app-initials', false);

    // Dépublication
    $this->actingAs($this->admin)
        ->put("/admin/applications/{$application->slug}", ['name' => 'APPLI DE TEST', 'slug' => 'appli-de-test', 'position' => 99, 'is_published' => 0, 'solutions' => [$visible->id]])
        ->assertSessionHasNoErrors();
    $this->get('/ecosysteme')->assertDontSee('APPLI DE TEST');
    $this->get('/')->assertDontSee('APPLI DE TEST');
    $this->get("/solutions/{$visible->slug}")->assertDontSee('APPLI DE TEST');

    // Suppression
    $this->actingAs($this->admin)->delete("/admin/applications/{$application->slug}")->assertRedirect('/admin/applications');
    expect(PlatformApplication::query()->where('slug', 'appli-de-test')->exists())->toBeFalse();
});

it('vidéos techniques : création, miniature YouTube, dépublication et suppression', function () {
    $this->actingAs($this->admin)
        ->post('/admin/tech-videos', [
            'label' => 'Vidéo de test',
            'slug' => 'video-de-test',
            'description' => 'Description de la vidéo de test.',
            'tags' => 'Démo, Scan',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'position' => 99,
            'is_published' => 1,
        ])
        ->assertSessionHasNoErrors();

    $video = TechVideo::query()->where('slug', 'video-de-test')->firstOrFail();

    $this->get('/videos-tech')
        ->assertSee('Vidéo de test')->assertSee('Description de la vidéo de test.')
        ->assertSee('Scan')
        ->assertSee('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false)
        ->assertSee('data-video-id="dQw4w9WgXcQ"', false);

    $this->actingAs($this->admin)
        ->put("/admin/tech-videos/{$video->slug}", ['label' => 'Vidéo de test', 'slug' => 'video-de-test', 'youtube_url' => $video->youtube_url, 'position' => 99, 'is_published' => 0])
        ->assertSessionHasNoErrors();
    $this->get('/videos-tech')->assertDontSee('Vidéo de test');

    $this->actingAs($this->admin)->delete("/admin/tech-videos/{$video->slug}")->assertRedirect('/admin/tech-videos');
    expect(TechVideo::query()->whereKey($video->id)->exists())->toBeFalse();
});

it('pages : chaque champ proposé s\'affiche sur la bonne page', function () {
    $home = ['name' => 'Accueil', 'eyebrow' => 'SURTITRE ACCUEIL', 'heading' => 'Titre accueil test', 'summary' => 'Résumé accueil test',
        'body' => 'Texte animé de test', 'cta_label' => 'Bouton accueil test', 'cta_url' => 'https://example.com/demo', 'seo_title' => 'SEO accueil test'];
    $this->actingAs($this->admin)->put('/admin/pages/home', $home)->assertSessionHasNoErrors();

    $this->get('/')->assertSee('SURTITRE ACCUEIL')->assertSee('Titre accueil test')->assertSee('Résumé accueil test')
        ->assertSee('Texte animé de test')->assertSee('Bouton accueil test')->assertSee('https://example.com/demo', false)
        ->assertSee('<title>SEO accueil test - ACCÈS EVENT</title>', false);

    $this->actingAs($this->admin)->put('/admin/pages/ecosystem', ['name' => 'Écosystème', 'heading' => 'Titre écosystème test', 'cta_label' => 'Bandeau interop test', 'body' => 'Texte interop test'])->assertSessionHasNoErrors();
    $this->get('/ecosysteme')->assertSee('Titre écosystème test')->assertSee('Bandeau interop test')->assertSee('Texte interop test');

    $this->actingAs($this->admin)->put('/admin/pages/about', ['name' => 'Qui sommes-nous', 'heading' => 'Titre à propos test', 'body' => "Paragraphe un test.\nParagraphe deux test."])->assertSessionHasNoErrors();
    $this->get('/qui-sommes-nous')->assertSee('Titre à propos test')->assertSee('Paragraphe un test.')->assertSee('Paragraphe deux test.');

    // Un champ non utilisé par la page n'est pas modifié, même s'il est envoyé
    $contactBody = SitePage::findByKey('contact')->body;
    $this->actingAs($this->admin)->put('/admin/pages/contact', ['name' => 'Contact', 'heading' => 'Titre contact test', 'cta_label' => 'Envoyer test', 'body' => 'Ne doit pas être enregistré'])->assertSessionHasNoErrors();
    $this->get('/contact')->assertSee('Titre contact test')->assertSee('Envoyer test');
    expect(SitePage::findByKey('contact')->body)->toBe($contactBody);
});

it('pages : le formulaire ne propose que les champs utilisés', function () {
    $this->actingAs($this->admin)->get('/admin/pages/contact/edit')
        ->assertSee('name="cta_label"', false)
        ->assertDontSee('name="body"', false)
        ->assertDontSee('name="is_published"', false);

    $this->actingAs($this->admin)->get('/admin/pages/home/edit')->assertSee('name="body"', false)->assertSee('Grand texte animé');
});

it('médias : ajout et suppression du fichier', function () {
    $this->actingAs($this->admin)
        ->post('/admin/media', ['file' => UploadedFile::fake()->image('visuel.jpg', 640, 480), 'name' => 'Visuel de test'])
        ->assertSessionHasNoErrors();

    $media = MediaAsset::query()->where('name', 'Visuel de test')->firstOrFail();
    expect(file_exists(public_path($media->path)))->toBeTrue();
    $this->actingAs($this->admin)->get('/admin/media')->assertSee('Visuel de test');

    $this->actingAs($this->admin)->delete("/admin/media/{$media->id}")->assertRedirect('/admin/media');
    expect(file_exists(public_path($media->path)))->toBeFalse()
        ->and(MediaAsset::query()->whereKey($media->id)->exists())->toBeFalse();
});

it('demandes : le formulaire public crée une demande traitée ensuite dans l\'admin', function () {
    Mail::fake();

    $this->post('/contact', [
        'full_name' => 'Client Test', 'company' => 'Société Test', 'email' => 'client@example.com',
        'event_type' => 'Festival', 'message' => 'Nous organisons un festival de 5 000 personnes.', 'website' => '',
    ])->assertSessionHasNoErrors();

    $demo = DemoRequest::query()->where('email', 'client@example.com')->firstOrFail();
    $this->actingAs($this->admin)->get('/admin/demo-requests')->assertSee('Client Test');

    $this->actingAs($this->admin)->put("/admin/demo-requests/{$demo->id}", ['status' => 'handled', 'internal_notes' => 'Rappelé'])->assertSessionHasNoErrors();
    expect($demo->fresh()->status)->toBe('handled')->and($demo->fresh()->handled_at)->not->toBeNull();
});

it('pied de page : les coordonnées s\'affichent sur toutes les pages et sur Contact', function () {
    $this->actingAs($this->admin)->put('/admin/settings/footer', [
        'footer_tagline' => 'Accroche pied de page test',
        'footer_email' => 'contact-test@example.com',
        'footer_phone' => '+225 01 02 03 04 05',
        'footer_whatsapp' => '+225 07 08 09 10 11',
        'footer_address' => 'Abidjan, Plateau',
        'footer_hours' => 'Du lundi au samedi',
        'footer_linkedin_url' => 'https://linkedin.com/company/test',
        'footer_facebook_url' => '#',
        'footer_legal_text' => 'Mention légale test',
    ])->assertSessionHasNoErrors();

    foreach (['/', '/solutions', '/ecosysteme', '/videos-tech', '/qui-sommes-nous', '/contact'] as $url) {
        $this->get($url)->assertSee('Accroche pied de page test')->assertSee('contact-test@example.com')->assertSee('Mention légale test')
            ->assertSee('https://linkedin.com/company/test', false);
    }

    $this->get('/contact')
        ->assertSee('href="tel:+2250102030405"', false)
        ->assertSee('href="https://wa.me/2250708091011"', false)
        ->assertSee('Abidjan, Plateau')->assertSee('Du lundi au samedi');
    // Le bouton WhatsApp de l'accueil utilise aussi ce numéro
    $this->get('/')->assertSee('https://wa.me/2250708091011', false);
});
