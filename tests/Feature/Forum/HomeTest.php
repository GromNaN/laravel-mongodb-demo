<?php

use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche la page d\'accueil', function () {
    $this->get(route('home'))->assertStatus(200);
});

it('affiche les catégories et forums', function () {
    $this->makeForum();

    $response = $this->get(route('home'));
    $response->assertSee('Test Category');
    $response->assertSee('Test Forum');
});

it('affiche les compteurs globaux', function () {
    $this->get(route('home'))
        ->assertSee('Total posts')
        ->assertSee('Total topics')
        ->assertSee('Total users');
});

it('affiche le lien pour créer un topic si connecté', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->get(route('forum.show', $forum->id))
        ->assertSee('New Topic');
});

it('n\'affiche pas le lien new topic pour un invité', function () {
    [, $forum] = $this->makeForum();

    $this->get(route('forum.show', $forum->id))
        ->assertDontSee('New Topic');
});
