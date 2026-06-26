<?php

use App\Models\User;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche le formulaire d\'inscription', function () {
    $this->get(route('register'))->assertStatus(200)->assertSee('Register');
});

it('inscrit un nouvel utilisateur', function () {
    $this->post(route('register'), [
        'username'              => 'newuser',
        'email'                 => 'newuser@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/');

    expect(User::where('username', 'newuser')->exists())->toBeTrue();
    $this->assertAuthenticated();
});

it('assigne le groupe member par défaut', function () {
    $this->post(route('register'), [
        'username'              => 'grouptest',
        'email'                 => 'grouptest@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('username', 'grouptest')->first();
    expect($user->group_id)->toBe(4);
});

it('refuse un username déjà pris', function () {
    $this->makeUser(['username' => 'existing', 'email' => 'first@test.com']);

    $this->post(route('register'), [
        'username'              => 'existing',
        'email'                 => 'second@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('username');
});

it('refuse un email déjà utilisé', function () {
    $this->makeUser(['username' => 'user1', 'email' => 'shared@test.com']);

    $this->post(route('register'), [
        'username'              => 'user2',
        'email'                 => 'shared@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

it('refuse un mot de passe trop court', function () {
    $this->post(route('register'), [
        'username'              => 'shortpass',
        'email'                 => 'shortpass@test.com',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');
});

it('refuse un username trop court', function () {
    $this->post(route('register'), [
        'username'              => 'x',
        'email'                 => 'x@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('username');
});

it('refuse un username trop long (>25)', function () {
    $this->post(route('register'), [
        'username'              => str_repeat('a', 26),
        'email'                 => 'toolong@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('username');
});
