<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche le formulaire de connexion', function () {
    $this->get(route('login'))->assertStatus(200)->assertSee('Login');
});

it('connecte avec les identifiants corrects (username)', function () {
    $user = $this->makeUser([
        'username' => 'logintest',
        'email'    => 'login@test.com',
        'password' => Hash::make('secret1234'),
    ]);

    $this->post(route('login'), ['login' => 'logintest', 'password' => 'secret1234'])
        ->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('connecte avec email', function () {
    $user = $this->makeUser([
        'username' => 'emaillogin',
        'email'    => 'emaillogin@test.com',
        'password' => Hash::make('secret1234'),
    ]);

    $this->post(route('login'), ['login' => 'emaillogin@test.com', 'password' => 'secret1234'])
        ->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('rejette un mot de passe incorrect', function () {
    $this->makeUser([
        'username' => 'wrongpass',
        'email'    => 'wrongpass@test.com',
        'password' => Hash::make('goodpassword'),
    ]);

    $this->post(route('login'), ['login' => 'wrongpass', 'password' => 'badpassword'])
        ->assertSessionHasErrors();

    $this->assertGuest();
});

it('déconnecte', function () {
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});
