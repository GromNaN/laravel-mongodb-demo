<?php

use App\Models\Topic;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('affiche un topic', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user, ['subject' => 'Topic visible']);

    $this->get(route('topic.show', $topic->id))
        ->assertStatus(200)
        ->assertSee('Topic visible');
});

it('affiche les posts du topic', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user);

    $this->get(route('topic.show', $topic->id))
        ->assertSee('This is the first post content.');
});

it('incrémente le compteur de vues', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user);

    expect($topic->num_views)->toBe(0);

    $this->get(route('topic.show', $topic->id));

    expect(Topic::find($topic->id)->num_views)->toBe(1);
});

it('retourne 404 pour un topic inexistant', function () {
    $this->get('/topic/000000000000000000000000')
        ->assertStatus(404);
});

it('abonne un utilisateur au topic', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $subscriber = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($subscriber)
        ->post(route('topic.subscribe', $topic->id))
        ->assertRedirect();

    $subscriber->refresh();
    expect($subscriber->topic_subscriptions)->toContain((string) $topic->id);
});

it('désabonne un utilisateur du topic', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);
    $subscriber = $this->makeUser(['topic_subscriptions' => [(string) $topic->id]]);

    $this->actingAs($subscriber)
        ->delete(route('topic.unsubscribe', $topic->id))
        ->assertRedirect();

    $subscriber->refresh();
    expect($subscriber->topic_subscriptions ?? [])->not->toContain((string) $topic->id);
});
