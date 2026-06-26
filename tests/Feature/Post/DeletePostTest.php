<?php

use App\Models\Post;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('redirige un invité vers login', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $user);

    $this->delete(route('post.destroy', $post->id))
        ->assertRedirect(route('login'));
});

it('refuse la suppression à un non-auteur', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $other = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $owner);
    $reply = $this->makePost($topic, $owner);

    $this->actingAs($other)
        ->delete(route('post.destroy', $reply->id))
        ->assertStatus(403);
});

it('permet à l\'auteur de supprimer son propre post', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $owner);
    $reply = $this->makePost($topic, $owner);

    $this->actingAs($owner)
        ->delete(route('post.destroy', $reply->id))
        ->assertRedirect();

    expect(Post::find($reply->id))->toBeNull();
});

it('permet à un admin de supprimer n\'importe quel post', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $admin = $this->makeAdmin();
    [$topic, $post] = $this->makeTopic($forum, $owner);
    $reply = $this->makePost($topic, $owner);

    $this->actingAs($admin)
        ->delete(route('post.destroy', $reply->id))
        ->assertRedirect();

    expect(Post::find($reply->id))->toBeNull();
});

it('décrémente num_replies du topic après suppression', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);
    $reply = $this->makePost($topic, $owner);

    $topic->increment('num_replies');

    $this->actingAs($owner)
        ->delete(route('post.destroy', $reply->id));

    $topic->refresh();
    expect($topic->num_replies)->toBe(0);
});
