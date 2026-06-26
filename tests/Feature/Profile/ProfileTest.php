<?php

use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche le profil d\'un utilisateur', function () {
    $user = $this->makeUser(['username' => 'JohnDoe']);

    $this->get(route('profile.show', $user->id))
        ->assertStatus(200)
        ->assertSee('JohnDoe');
});

it('retourne 404 pour un profil inexistant', function () {
    $this->get(route('profile.show', '000000000000000000000000'))
        ->assertStatus(404);
});

it('redirige un invité vers login pour l\'édition', function () {
    $user = $this->makeUser();

    $this->get(route('profile.edit', $user->id))
        ->assertRedirect(route('login'));
});

it('affiche le formulaire d\'édition pour le propriétaire', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->get(route('profile.edit', $user->id))
        ->assertStatus(200);
});

it('refuse l\'édition d\'un autre profil', function () {
    $user = $this->makeUser();
    $other = $this->makeUser();

    $this->actingAs($other)
        ->get(route('profile.edit', $user->id))
        ->assertStatus(403);
});

it('met à jour la signature', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->put(route('profile.update', $user->id), [
            'email' => $user->email,
            'signature' => 'Ma nouvelle signature.',
        ])
        ->assertRedirect();

    $user->refresh();
    expect($user->signature)->toBe('Ma nouvelle signature.');
});

it('refuse un email invalide', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->put(route('profile.update', $user->id), [
            'email' => 'pas-un-email',
        ])
        ->assertSessionHasErrors('email');
});

it('permet à un admin de modifier n\'importe quel profil', function () {
    $user = $this->makeUser();
    $admin = $this->makeAdmin();

    $this->actingAs($admin)
        ->get(route('profile.edit', $user->id))
        ->assertStatus(200);
});
