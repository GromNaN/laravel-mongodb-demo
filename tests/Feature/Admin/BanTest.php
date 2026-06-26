<?php

use App\Models\Ban;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche la liste des bans', function () {
    $admin = $this->makeAdmin();

    $this->actingAs($admin)
        ->get(route('admin.bans.index'))
        ->assertStatus(200);
});

it('refuse la liste des bans à un membre', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->get(route('admin.bans.index'))
        ->assertStatus(403);
});

it('crée un ban', function () {
    $admin = $this->makeAdmin();
    $target = $this->makeUser(['username' => 'MauvaisePersonne']);

    $this->actingAs($admin)
        ->post(route('admin.bans.store'), [
            'username' => 'MauvaisePersonne',
            'ip' => '',
            'email' => '',
            'message' => 'Comportement inapproprié.',
            'expire' => '',
        ])
        ->assertRedirect();

    expect(Ban::where('username', 'MauvaisePersonne')->exists())->toBeTrue();
});

it('supprime un ban', function () {
    $admin = $this->makeAdmin();
    $ban = Ban::create([
        'username' => 'Banni',
        'ip' => null,
        'email' => null,
        'message' => 'Test',
        'expire' => null,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.bans.destroy', $ban->id))
        ->assertRedirect();

    expect(Ban::find($ban->id))->toBeNull();
});
