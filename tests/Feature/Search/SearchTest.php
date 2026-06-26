<?php

use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche le formulaire de recherche', function () {
    $this->get(route('search.index'))
        ->assertStatus(200);
});

it('retourne des résultats pour un mot-clé', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    $this->makeTopic($forum, $user, ['subject' => 'Discussion sur Laravel']);

    $this->get(route('search.index', ['keywords' => 'Laravel']))
        ->assertStatus(200)
        ->assertSee('Discussion sur Laravel');
});

it('ne retourne pas de résultats hors sujet', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    $this->makeTopic($forum, $user, ['subject' => 'Discussion sur Laravel']);

    $this->get(route('search.index', ['keywords' => 'Symfony']))
        ->assertStatus(200)
        ->assertDontSee('Discussion sur Laravel');
});

it('gère une recherche vide', function () {
    $this->get(route('search.index', ['keywords' => '']))
        ->assertStatus(200);
});
