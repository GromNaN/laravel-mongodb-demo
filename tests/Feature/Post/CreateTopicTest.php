<?php

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('redirige un invité vers login', function () {
    [, $forum] = $this->makeForum();

    $this->get(route('post.create', $forum->id))
        ->assertRedirect(route('login'));
});

it('affiche le formulaire de création pour un membre', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->get(route('post.create', $forum->id))
        ->assertStatus(200);
});

it('crée un topic et le premier post', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('post.store', $forum->id), [
            'subject' => 'Mon nouveau topic',
            'message' => 'Contenu du premier message.',
        ])
        ->assertRedirect();

    expect(Topic::where('subject', 'Mon nouveau topic')->exists())->toBeTrue();
    expect(Post::where('poster', $user->username)->exists())->toBeTrue();
});

it('incrémente les stats du forum', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)->post(route('post.store', $forum->id), [
        'subject' => 'Topic stats',
        'message' => 'Message.',
    ]);

    $forum->refresh();
    expect($forum->num_topics)->toBe(1);
    expect($forum->num_posts)->toBe(1);
});

it('incrémente le num_posts de l\'utilisateur', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)->post(route('post.store', $forum->id), [
        'subject' => 'Topic user posts',
        'message' => 'Message.',
    ]);

    $user->refresh();
    expect($user->num_posts)->toBe(1);
});

it('refuse un subject vide', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('post.store', $forum->id), ['subject' => '', 'message' => 'Ok.'])
        ->assertSessionHasErrors('subject');
});

it('refuse un subject > 70 caractères', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('post.store', $forum->id), ['subject' => str_repeat('a', 71), 'message' => 'Ok.'])
        ->assertSessionHasErrors('subject');
});

it('refuse un message vide', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();

    $this->actingAs($user)
        ->post(route('post.store', $forum->id), ['subject' => 'Sujet valide', 'message' => ''])
        ->assertSessionHasErrors('message');
});
