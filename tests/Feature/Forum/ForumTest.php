<?php

use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche un forum', function () {
    [, $forum] = $this->makeForum();

    $this->get(route('forum.show', $forum->id))
        ->assertStatus(200)
        ->assertSee($forum->name);
});

it('liste les topics du forum', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    $this->makeTopic($forum, $user, ['subject' => 'Mon topic de test']);

    $this->get(route('forum.show', $forum->id))
        ->assertSee('Mon topic de test');
});

it('retourne 404 pour un forum inexistant', function () {
    $this->get('/forum/000000000000000000000000')
        ->assertStatus(404);
});

it('affiche les topics sticky en premier', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    $this->makeTopic($forum, $user, ['subject' => 'Topic normal']);
    $this->makeTopic($forum, $user, ['subject' => 'Topic épinglé', 'sticky' => true]);

    $html = $this->get(route('forum.show', $forum->id))->getContent();

    expect(strpos($html, 'Topic épinglé'))->toBeLessThan(strpos($html, 'Topic normal'));
});

it('abonne un utilisateur connecté au forum', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('forum.subscribe', $forum->id))
        ->assertRedirect();

    $user->refresh();
    expect($user->forum_subscriptions)->toContain((string) $forum->id);
});

it('désabonne un utilisateur du forum', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser(['forum_subscriptions' => [(string) $forum->id]]);

    $this->actingAs($user)
        ->delete(route('forum.unsubscribe', $forum->id))
        ->assertRedirect();

    $user->refresh();
    expect($user->forum_subscriptions ?? [])->not->toContain((string) $forum->id);
});
