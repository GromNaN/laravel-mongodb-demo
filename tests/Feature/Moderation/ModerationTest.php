<?php

use App\Models\Topic;
use Tests\Feature\Traits\RefreshMongoDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshMongoDatabase::class)
    ->beforeEach(fn () => $this->setUpRefreshMongoDatabase());

it('un modérateur peut fermer un topic', function () {
    [, $forum] = $this->makeForum();
    $mod = $this->makeModerator($forum);
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($mod)
        ->post(route('topic.close', $topic->id))
        ->assertRedirect();

    expect(Topic::find($topic->id)->closed)->toBeTrue();
});

it('un modérateur peut ouvrir un topic fermé', function () {
    [, $forum] = $this->makeForum();
    $mod = $this->makeModerator($forum);
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner, ['closed' => true]);

    $this->actingAs($mod)
        ->post(route('topic.open', $topic->id))
        ->assertRedirect();

    expect(Topic::find($topic->id)->closed)->toBeFalse();
});

it('un modérateur peut épingler un topic', function () {
    [, $forum] = $this->makeForum();
    $mod = $this->makeModerator($forum);
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($mod)
        ->post(route('topic.stick', $topic->id))
        ->assertRedirect();

    expect(Topic::find($topic->id)->sticky)->toBeTrue();
});

it('un modérateur peut désépingler un topic', function () {
    [, $forum] = $this->makeForum();
    $mod = $this->makeModerator($forum);
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner, ['sticky' => true]);

    $this->actingAs($mod)
        ->post(route('topic.unstick', $topic->id))
        ->assertRedirect();

    expect(Topic::find($topic->id)->sticky)->toBeFalse();
});

it('refuse la modération à un membre normal', function () {
    [, $forum] = $this->makeForum();
    $user = $this->makeUser();
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($user)
        ->post(route('topic.close', $topic->id))
        ->assertStatus(403);
});

it('un admin peut modérer sans être modérateur du forum', function () {
    [, $forum] = $this->makeForum();
    $admin = $this->makeAdmin();
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($admin)
        ->post(route('topic.close', $topic->id))
        ->assertRedirect();

    expect(Topic::find($topic->id)->closed)->toBeTrue();
});

it('un modérateur peut déplacer un topic vers un autre forum', function () {
    [, $forum] = $this->makeForum();
    [, $targetForum] = $this->makeForum(['name' => 'Forum cible']);
    $mod = $this->makeModerator($forum);
    $owner = $this->makeUser();
    [$topic] = $this->makeTopic($forum, $owner);

    $this->actingAs($mod)
        ->post(route('topic.move', $topic->id), ['forum_id' => (string) $targetForum->id])
        ->assertRedirect();

    expect(Topic::find($topic->id)->forum_id)->toBe((string) $targetForum->id);
});
