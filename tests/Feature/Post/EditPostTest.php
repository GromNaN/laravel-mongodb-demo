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

    $this->get(route('post.edit', $post->id))
        ->assertRedirect(route('login'));
});

it('affiche le formulaire d\'édition pour l\'auteur', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $user);

    $this->actingAs($user)
        ->get(route('post.edit', $post->id))
        ->assertStatus(200)
        ->assertSee($post->message);
});

it('refuse l\'accès à un non-auteur', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $other = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $owner);

    $this->actingAs($other)
        ->get(route('post.edit', $post->id))
        ->assertStatus(403);
});

it('met à jour le message du post', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $user);

    $this->actingAs($user)
        ->put(route('post.update', $post->id), [
            'message' => 'Message modifié.',
        ])
        ->assertRedirect();

    expect(Post::find($post->id)->message)->toBe('Message modifié.');
});

it('refuse la mise à jour par un non-auteur', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $other = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $owner);

    $this->actingAs($other)
        ->put(route('post.update', $post->id), ['message' => 'Tentative.'])
        ->assertStatus(403);
});

it('permet à un admin de modifier n\'importe quel post', function () {
    [, $forum] = $this->makeForum();
    $owner = $this->makeUser();
    $admin = $this->makeAdmin();
    [$topic, $post] = $this->makeTopic($forum, $owner);

    $this->actingAs($admin)
        ->put(route('post.update', $post->id), ['message' => 'Modifié par admin.'])
        ->assertRedirect();

    expect(Post::find($post->id)->message)->toBe('Modifié par admin.');
});

it('refuse un message vide', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    [$topic, $post] = $this->makeTopic($forum, $user);

    $this->actingAs($user)
        ->put(route('post.update', $post->id), ['message' => ''])
        ->assertSessionHasErrors('message');
});
