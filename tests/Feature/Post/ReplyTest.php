<?php

use App\Models\Post;
use App\Models\Topic;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('redirige un invité vers login', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user);

    $this->get(route('post.reply', $topic->id))
        ->assertRedirect(route('login'));
});

it('affiche le formulaire de réponse', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user);

    $this->actingAs($user)
        ->get(route('post.reply', $topic->id))
        ->assertStatus(200);
});

it('crée une réponse et met à jour les stats du topic', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $replier = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($replier)
        ->post(route('post.storeReply', $topic->id), [
            'message' => 'Ma réponse au topic.',
        ])
        ->assertRedirect();

    expect(Post::where('topic_id', (string) $topic->id)->count())->toBe(2);

    $topic->refresh();
    expect($topic->num_replies)->toBe(1);
    expect($topic->last_post['poster'])->toBe($replier->username);
});

it('met à jour les stats du forum après une réponse', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $replier = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($replier)
        ->post(route('post.storeReply', $topic->id), ['message' => 'Réponse.']);

    $forum->refresh();
    expect($forum->num_posts)->toBe(2);
});

it('incrémente le num_posts du répondant', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $replier = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($replier)
        ->post(route('post.storeReply', $topic->id), ['message' => 'Réponse.']);

    $replier->refresh();
    expect($replier->num_posts)->toBe(1);
});

it('refuse une réponse à un topic fermé', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $replier = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner, ['closed' => true]);

    $this->actingAs($replier)
        ->post(route('post.storeReply', $topic->id), ['message' => 'Tentative.'])
        ->assertStatus(403);
});

it('refuse un message vide', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $user);

    $this->actingAs($user)
        ->post(route('post.storeReply', $topic->id), ['message' => ''])
        ->assertSessionHasErrors('message');
});
