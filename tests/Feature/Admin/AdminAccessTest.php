<?php

use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('redirige un invité vers login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

it('refuse l\'accès à un membre normal', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);
});

it('autorise un admin', function () {
    $admin = $this->makeAdmin();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200);
});

it('affiche la liste des utilisateurs en admin', function () {
    $admin = $this->makeAdmin();
    $this->makeUser(['username' => 'MembreVisible']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertStatus(200)
        ->assertSee('MembreVisible');
});

it('affiche la liste des forums en admin', function () {
    $admin = $this->makeAdmin();
    [, $forum] = $this->makeForum(['name' => 'Forum Admin Test']);

    $this->actingAs($admin)
        ->get(route('admin.forums.index'))
        ->assertStatus(200)
        ->assertSee('Forum Admin Test');
});
