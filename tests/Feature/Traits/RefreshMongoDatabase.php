<?php

namespace Tests\Feature\Traits;

use App\Models\Ban;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumConfig;
use App\Models\Group;
use App\Models\OnlineUser;
use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait RefreshMongoDatabase
{
    protected function setUpRefreshMongoDatabase(): void
    {
        foreach ([User::class, Group::class, Category::class, Forum::class, Topic::class, Post::class, Ban::class, Report::class, OnlineUser::class, ForumConfig::class] as $model) {
            $model::truncate();
        }

        Group::insert([
            ['title' => 'Administrators', 'user_title' => 'Administrator', 'moderator' => true, 'mod_edit_users' => true, 'mod_rename_users' => true, 'mod_change_passwords' => true, 'mod_ban_users' => true, 'mod_promote_users' => true, 'post_replies' => true, 'post_topics' => true, 'edit_posts' => true, 'delete_posts' => true, 'delete_topics' => true, 'set_title' => true, 'search' => true, 'search_users' => true, 'send_email' => true, 'post_flood' => 0, 'search_flood' => 0, 'email_flood' => 0, 'report_flood' => 0, 'promote_min_posts' => 0, 'promote_next_group' => 0],
            ['title' => 'Moderators', 'user_title' => 'Moderator', 'moderator' => true, 'mod_edit_users' => true, 'mod_rename_users' => false, 'mod_change_passwords' => false, 'mod_ban_users' => true, 'mod_promote_users' => false, 'post_replies' => true, 'post_topics' => true, 'edit_posts' => true, 'delete_posts' => true, 'delete_topics' => true, 'set_title' => true, 'search' => true, 'search_users' => true, 'send_email' => true, 'post_flood' => 0, 'search_flood' => 0, 'email_flood' => 60, 'report_flood' => 0, 'promote_min_posts' => 0, 'promote_next_group' => 0],
            ['title' => 'Guests', 'user_title' => 'Guest', 'moderator' => false, 'mod_edit_users' => false, 'mod_rename_users' => false, 'mod_change_passwords' => false, 'mod_ban_users' => false, 'mod_promote_users' => false, 'post_replies' => false, 'post_topics' => false, 'edit_posts' => false, 'delete_posts' => false, 'delete_topics' => false, 'set_title' => false, 'search' => true, 'search_users' => true, 'send_email' => false, 'post_flood' => 0, 'search_flood' => 60, 'email_flood' => 0, 'report_flood' => 0, 'promote_min_posts' => 0, 'promote_next_group' => 0],
            ['title' => 'Members', 'user_title' => 'Member', 'moderator' => false, 'mod_edit_users' => false, 'mod_rename_users' => false, 'mod_change_passwords' => false, 'mod_ban_users' => false, 'mod_promote_users' => false, 'post_replies' => true, 'post_topics' => true, 'edit_posts' => true, 'delete_posts' => false, 'delete_topics' => false, 'set_title' => false, 'search' => true, 'search_users' => true, 'send_email' => true, 'post_flood' => 60, 'search_flood' => 30, 'email_flood' => 60, 'report_flood' => 60, 'promote_min_posts' => 0, 'promote_next_group' => 0],
        ]);
    }

    protected function makeUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'username' => 'user_'.uniqid(),
            'email' => 'user_'.uniqid().'@test.com',
            'password' => Hash::make('password123'),
            'group_id' => 4,
            'num_posts' => 0,
            'registered' => now(),
            'last_visit' => now(),
            'is_banned' => false,
        ], $overrides));
    }

    protected function makeAdmin(array $overrides = []): User
    {
        return $this->makeUser(array_merge(['group_id' => 1], $overrides));
    }

    protected function makeModerator(Forum|array $forumOrOverrides = [], array $overrides = []): User
    {
        if ($forumOrOverrides instanceof Forum) {
            $user = $this->makeUser(array_merge(['group_id' => 2], $overrides));
            $mods = $forumOrOverrides->moderators ?? [];
            $mods[] = $user->username;
            $forumOrOverrides->moderators = $mods;
            $forumOrOverrides->save();

            return $user;
        }

        return $this->makeUser(array_merge(['group_id' => 2], $forumOrOverrides));
    }

    protected function makeForum(array $overrides = []): array
    {
        $category = Category::create(['name' => 'Test Category', 'position' => 1]);
        $forum = Forum::create(array_merge([
            'name' => 'Test Forum',
            'description' => 'A test forum',
            'category_id' => (string) $category->id,
            'position' => 1,
            'num_topics' => 0,
            'num_posts' => 0,
            'moderators' => [],
            'permissions' => [],
        ], $overrides));

        return [$category, $forum];
    }

    protected function makeTopic(Forum $forum, User $user, array $overrides = []): array
    {
        $topic = Topic::create(array_merge([
            'forum_id' => (string) $forum->id,
            'subject' => 'Test Topic '.uniqid(),
            'poster' => $user->username,
            'poster_id' => (string) $user->id,
            'posted' => now(),
            'num_views' => 0,
            'num_replies' => 0,
            'closed' => false,
            'sticky' => false,
        ], $overrides));

        $post = Post::create([
            'topic_id' => (string) $topic->id,
            'forum_id' => (string) $forum->id,
            'poster' => $user->username,
            'poster_id' => (string) $user->id,
            'poster_ip' => '127.0.0.1',
            'posted' => now(),
            'message' => 'This is the first post content.',
            'num' => 1,
        ]);

        $topic->update(['first_post_id' => (string) $post->id]);

        $forum->increment('num_topics');
        $forum->increment('num_posts');

        return [$topic, $post];
    }

    protected function makePost(Topic $topic, User $user, array $overrides = []): Post
    {
        $num = Post::where('topic_id', (string) $topic->id)->count() + 1;

        return Post::create(array_merge([
            'topic_id' => (string) $topic->id,
            'forum_id' => $topic->forum_id,
            'poster' => $user->username,
            'poster_id' => (string) $user->id,
            'poster_ip' => '127.0.0.1',
            'posted' => now(),
            'message' => 'A reply post.',
            'num' => $num,
        ], $overrides));
    }
}
