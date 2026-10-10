<?php

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeOwner(): User
{
    $owner = User::factory()->create(['name' => 'Propriétaire', 'role' => 'admin']);
    $owner->forceFill(['is_owner' => true])->save();

    return $owner;
}

function makeAdmin(string $name = 'Second admin'): User
{
    return User::factory()->create(['name' => $name, 'role' => 'admin']);
}

it('lets an admin create another admin and logs it', function () {
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->post('/admin/users', [
            'name' => 'Nouvel admin',
            'email' => 'nouveau@example.com',
            'password' => 'MotDePasse2026',
            'password_confirmation' => 'MotDePasse2026',
        ])
        ->assertRedirect('/admin/users');

    $created = User::query()->where('email', 'nouveau@example.com')->firstOrFail();

    expect($created->isAdmin())->toBeTrue()
        ->and($created->isOwner())->toBeFalse()
        ->and(ActivityLog::query()->where('action', 'admin.users.store')->value('description'))
        ->toBe("a créé l'administrateur « Nouvel admin »");
});

it('never lets anyone delete the owner', function () {
    $owner = makeOwner();
    $admin = makeAdmin();

    $this->actingAs($admin)->delete("/admin/users/{$owner->id}")->assertSessionHasErrors('user');
    $this->actingAs($owner)->delete("/admin/users/{$owner->id}")->assertSessionHasErrors('user');

    expect(User::query()->whereKey($owner->id)->exists())->toBeTrue()
        ->and(ActivityLog::query()->where('action', 'admin.users.destroy')->exists())->toBeFalse();
});

it('only lets the owner edit the owner account', function () {
    $owner = makeOwner();
    $admin = makeAdmin();

    $this->actingAs($admin)->get("/admin/users/{$owner->id}/edit")->assertForbidden();
    $this->actingAs($admin)->put("/admin/users/{$owner->id}", ['name' => 'Piraté', 'email' => $owner->email])->assertForbidden();
    $this->actingAs($owner)->get("/admin/users/{$owner->id}/edit")->assertOk();

    expect($owner->fresh()->name)->toBe('Propriétaire');
});

it('lets an admin delete another admin but not himself', function () {
    $admin = makeAdmin();
    $other = makeAdmin('Autre admin');

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHasErrors('user');
    $this->actingAs($admin)->delete("/admin/users/{$other->id}")->assertRedirect('/admin/users');

    expect(User::query()->whereKey($admin->id)->exists())->toBeTrue()
        ->and(User::query()->whereKey($other->id)->exists())->toBeFalse();
});

it('keeps the password when it is left empty on update', function () {
    $admin = makeAdmin();
    $hash = $admin->password;

    $this->actingAs($admin)
        ->put("/admin/users/{$admin->id}", ['name' => 'Renommé', 'email' => $admin->email, 'password' => ''])
        ->assertRedirect('/admin/users');

    expect($admin->fresh()->password)->toBe($hash)
        ->and($admin->fresh()->name)->toBe('Renommé');
});

it('shows the activity log to the owner only', function () {
    $owner = makeOwner();
    $admin = makeAdmin();

    $this->actingAs($admin)->get('/admin/activity')->assertForbidden();
    $this->actingAs($admin)->get('/admin')->assertDontSee("Journal d'activité", false);

    $this->actingAs($owner)->get('/admin/activity')->assertOk()->assertSee('Qui a fait quoi');
    $this->actingAs($owner)->get('/admin')->assertSee("Journal d'activité", false);
});

it('logs successful and failed logins but not rejected form submissions', function () {
    $admin = makeAdmin();

    $this->post('/admin/login', ['email' => $admin->email, 'password' => 'mauvais'])->assertSessionHasErrors('email');
    $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');

    $this->actingAs($admin)->post('/admin/users', ['name' => '', 'email' => 'invalide'])->assertSessionHasErrors();

    expect(ActivityLog::query()->pluck('action')->all())->toBe(['auth.failed', 'auth.login']);
});

it('lets an admin replace the logo everywhere, then restore the original', function () {
    $this->seed();
    $admin = makeAdmin();

    $this->actingAs($admin)
        ->put('/admin/settings/brand', ['logo_file' => \Illuminate\Http\UploadedFile::fake()->image('logo.png', 400, 120)])
        ->assertRedirect('/admin/settings/brand');

    $logo = \App\Models\SiteSetting::uploadedLogo();

    expect($logo)->toStartWith('/uploads/brand/')
        ->and(file_exists(public_path(ltrim($logo, '/'))))->toBeTrue()
        ->and(ActivityLog::query()->where('action', 'admin.settings.brand.update')->exists())->toBeTrue();

    $this->get('/')->assertSee($logo, false);
    $this->actingAs($admin)->get('/admin')->assertSee($logo, false);

    $this->actingAs($admin)->put('/admin/settings/brand', ['reset_logo' => 1])->assertRedirect('/admin/settings/brand');

    expect(\App\Models\SiteSetting::uploadedLogo())->toBeNull()
        ->and(file_exists(public_path(ltrim($logo, '/'))))->toBeFalse();
    $this->get('/')->assertSee('images/logo/accesslogo.png', false);
});

it('refuses SVG logos', function () {
    $this->actingAs(makeAdmin())
        ->put('/admin/settings/brand', ['logo_file' => \Illuminate\Http\UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml')])
        ->assertSessionHasErrors('logo_file');

    expect(\App\Models\SiteSetting::uploadedLogo())->toBeNull();
});
