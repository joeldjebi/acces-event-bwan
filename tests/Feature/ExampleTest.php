<?php

use App\Mail\DemoRequestReceived;
use App\Models\DemoRequest;
use App\Models\MediaAsset;
use App\Models\PlatformApplication;
use App\Models\SiteSetting;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('renders the public foundation page', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/')
        ->assertOk()
        ->assertSee('ACCÈS EVENT')
        ->assertSee('Tout l&#039;événement, dans une seule technologie.', false);
});

it('redirects guests from admin dashboard to login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

it('renders the admin login page', function () {
    $this->withoutVite();

    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Connexion administrateur');
});

it('allows an admin user to access the dashboard', function () {
    $this->withoutVite();

    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'password',
    ]);

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect('/admin');

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Tableau de bord ACCÈS EVENT')
        ->assertSee('Pilotage');
});

it('rejects a user without back office rights', function () {
    $user = User::factory()->create([
        'role' => 'visitor',
        'password' => 'password',
    ]);

    $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('renders seeded solutions on public pages', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/solutions')
        ->assertOk()
        ->assertSee('Billetterie 360')
        ->assertSee('Contrôle d&#039;accès', false);

    $this->get('/solutions/billetterie-360')
        ->assertOk()
        ->assertSee('Vendez partout, encaissez en sécurité');
});

it('renders the public presentation pages', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/ecosysteme')
        ->assertOk()
        ->assertSee('Toutes nos plateformes connectées')
        ->assertSee('BIG ACCESS');

    $this->get('/qui-sommes-nous')
        ->assertOk()
        ->assertSee('Une technologie événementielle pensée')
        ->assertSee('EIGTH CORPORATE');

    $this->get('/contact')
        ->assertOk()
        ->assertSee('Demandez une démonstration personnalisée')
        ->assertSee('Envoyer la demande');
});

it('renders seeded platform applications on the ecosystem page', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/ecosysteme')
        ->assertOk()
        ->assertSee('ACCÈS TICKETS')
        ->assertSee('Billetterie 360')
        ->assertSee('FINEO PAY, X PAYE &amp; JEKO', false);
});

it('stores a demo request and sends a notification email', function () {
    Mail::fake();
    $this->seed();

    $this->post('/contact', [
        'full_name' => 'Client Test',
        'company' => 'ACME Events',
        'email' => 'client@example.com',
        'phone' => '+22500000000',
        'event_type' => 'Festival',
        'selected_solution' => 'Billetterie 360',
        'message' => 'Nous voulons une demonstration pour notre prochain festival.',
    ])->assertRedirect('/contact');

    $this->assertDatabaseHas('demo_requests', [
        'full_name' => 'Client Test',
        'status' => 'new',
        'selected_solution' => 'Billetterie 360',
    ]);

    Mail::assertSent(DemoRequestReceived::class);
});

it('lets an admin process a demo request', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $demoRequest = DemoRequest::query()->create([
        'full_name' => 'Client Test',
        'email' => 'client@example.com',
        'message' => 'Message de test assez long.',
    ]);

    $this->actingAs($admin)
        ->put("/admin/demo-requests/{$demoRequest->id}", [
            'status' => 'handled',
            'internal_notes' => 'Contact repondu par WhatsApp.',
        ])
        ->assertRedirect("/admin/demo-requests/{$demoRequest->id}");

    $this->assertDatabaseHas('demo_requests', [
        'id' => $demoRequest->id,
        'status' => 'handled',
        'internal_notes' => 'Contact repondu par WhatsApp.',
    ]);

    expect($demoRequest->fresh()->handled_at)->not->toBeNull();
});

it('lets an admin create a solution', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => 'password',
    ]);

    $this->actingAs($admin)
        ->post('/admin/solutions', [
            'title' => 'Solution test',
            'slug' => 'solution-test',
            'subtitle' => 'Accroche test',
            'short_description' => 'Description courte test',
            'content' => 'Contenu test',
            'image_file' => UploadedFile::fake()->image('domaine.jpg', 1200, 800),
            'position' => 50,
            'is_published' => 1,
            'benefits' => "Benefice test | Description benefice\nSecond benefice | Autre description",
            'tools' => 'Outil test | Description outil',
        ])
        ->assertRedirect('/admin/solutions/solution-test/edit');

    $this->assertDatabaseHas('solutions', [
        'slug' => 'solution-test',
        'is_published' => true,
    ]);

    $solution = Solution::query()->where('slug', 'solution-test')->firstOrFail();

    expect($solution->image_path)->toContain('/uploads/domaines/');
    expect(file_exists(public_path(ltrim($solution->image_path, '/'))))->toBeTrue();

    $this->assertDatabaseHas('solution_benefits', [
        'title' => 'Benefice test',
    ]);

    $this->assertDatabaseHas('solution_tools', [
        'name' => 'Outil test',
    ]);
});

it('lets an admin create a platform application', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $solution = Solution::query()->create([
        'title' => 'Solution associee',
        'slug' => 'solution-associee',
        'position' => 1,
        'is_published' => true,
    ]);

    $this->actingAs($admin)
        ->post('/admin/applications', [
            'name' => 'APP TEST',
            'slug' => 'app-test',
            'role' => 'Role applicatif test',
            'description' => 'Description applicative test',
            'logo_file' => UploadedFile::fake()->image('app-logo.png', 800, 800),
            'position' => 20,
            'is_published' => 1,
            'solutions' => [$solution->id],
        ])
        ->assertRedirect('/admin/applications/app-test/edit');

    $this->assertDatabaseHas('platform_applications', [
        'slug' => 'app-test',
        'is_published' => true,
    ]);

    $application = PlatformApplication::query()->where('slug', 'app-test')->firstOrFail();

    expect($application->logo_path)->toContain('/uploads/applications/');
    expect(file_exists(public_path(ltrim($application->logo_path, '/'))))->toBeTrue();

    $this->assertDatabaseHas('platform_application_solution', [
        'platform_application_id' => $application->id,
        'solution_id' => $solution->id,
    ]);
});

it('lets an admin update a site page', function () {
    $this->seed();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->put('/admin/pages/home', [
            'name' => 'Accueil',
            'eyebrow' => 'Test eyebrow',
            'heading' => 'Titre accueil modifie',
            'summary' => 'Resume accueil modifie',
            'body' => 'Corps accueil modifie',
            'cta_label' => 'CTA modifie',
            'cta_url' => '/contact',
            'seo_title' => 'SEO accueil modifie',
            'seo_description' => 'SEO description modifiee',
            'is_published' => 1,
        ])
        ->assertRedirect('/admin/pages/home/edit');

    $this->assertDatabaseHas('site_pages', [
        'key' => 'home',
        'heading' => 'Titre accueil modifie',
        'seo_title' => 'SEO accueil modifie',
    ]);

    $this->withoutVite();

    $this->get('/')
        ->assertOk()
        ->assertSee('Titre accueil modifie')
        ->assertSee('CTA modifie');
});

it('lets an admin upload a media asset', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->post('/admin/media', [
            'file' => UploadedFile::fake()->image('hero.jpg', 1200, 800),
            'name' => 'Hero image',
            'alt_text' => 'Image hero ACCES EVENT',
        ])
        ->assertRedirect('/admin/media');

    $media = MediaAsset::query()->firstOrFail();

    expect($media->name)->toBe('Hero image');
    expect($media->url)->toContain('/uploads/media/');

    expect(file_exists(public_path($media->path)))->toBeTrue();
});

it('renders seo metadata and discovery files', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="http://localhost">', false)
        ->assertSee('<meta property="og:site_name" content="ACCÈS EVENT">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<loc>http://localhost/solutions/billetterie-360</loc>', false);

    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: http://localhost/sitemap.xml');
});

it('adds security headers to web responses', function () {
    $this->withoutVite();
    $this->seed();

    $this->get('/')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

it('rate limits admin login attempts', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.10'])
            ->post('/admin/login', [
                'email' => 'missing@example.com',
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('email');
    }

    $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.10'])
        ->post('/admin/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])
        ->assertStatus(429);
});

it('ignores contact submissions that fill the honeypot field', function () {
    Mail::fake();
    $this->seed();

    $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.20'])
        ->post('/contact', [
            'full_name' => 'Spam Bot',
            'email' => 'spam@example.com',
            'message' => 'Message assez long mais piege.',
            'website' => 'https://spam.example.com',
        ])
        ->assertRedirect('/contact');

    $this->assertDatabaseMissing('demo_requests', [
        'email' => 'spam@example.com',
    ]);

    Mail::assertNothingSent();
});

it('lets an admin manage public footer settings', function () {
    $this->seed();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->put('/admin/settings/footer', [
            'footer_tagline' => 'Footer premium test',
            'footer_address' => 'Abidjan Plateau',
            'footer_phone' => '+225 01 02 03 04',
            'footer_email' => 'footer@example.com',
            'footer_hours' => '24/7 sur evenement',
            'footer_whatsapp' => '+225 05 06 07 08',
            'footer_linkedin_url' => 'https://linkedin.com/company/acces-event',
            'footer_facebook_url' => 'https://facebook.com/acces-event',
            'footer_legal_text' => 'Mention footer test',
        ])
        ->assertRedirect('/admin/settings/footer');

    $this->assertDatabaseHas('site_settings', [
        'key' => 'footer_tagline',
        'value' => 'Footer premium test',
    ]);

    expect(SiteSetting::footerData()['footer_email'])->toBe('footer@example.com');

    $this->withoutVite();

    $this->get('/')
        ->assertOk()
        ->assertSee('Footer premium test')
        ->assertSee('footer@example.com')
        ->assertSee('Mention footer test');
});
