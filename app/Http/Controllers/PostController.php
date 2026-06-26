<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreTopicRequest;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostController extends Controller
{
    public function create(Forum $forum): View
    {
        return view('post.create', compact('forum'));
    }

    public function store(StoreTopicRequest $request, Forum $forum): RedirectResponse
    {
        $user = Auth::user();
        $now = now();

        $topic = $forum->topics()->create([
            'subject' => $request->input('subject'),
            'poster' => $user->username,
            'poster_id' => $user->id,
            'posted' => $now,
        ]);

        $post = $topic->posts()->create([
            'forum_id' => $forum->id,
            'poster' => $user->username,
            'poster_id' => $user->id,
            'poster_ip' => $request->ip(),
            'posted' => $now,
            'message' => $request->input('message'),
            'num' => 1,
        ]);

        $topic->first_post_id = $post->id;
        $topic->last_post = [
            'time' => $now->timestamp,
            'poster' => $user->username,
            'poster_id' => $user->id,
            'post_id' => $post->id,
            'num' => 1,
        ];
        $topic->save();

        $forum->increment('num_topics');
        $forum->increment('num_posts');
        $forum->last_post = [
            'time' => $now->timestamp,
            'poster' => $user->username,
            'post_id' => $post->id,
            'topic_id' => $topic->id,
            'subject' => $topic->subject,
            'num' => 1,
        ];
        $forum->save();

        $user->increment('num_posts');
        $user->save();

        return redirect()->route('topic.show', $topic->id);
    }

    public function reply(Topic $topic): View
    {
        abort_if($topic->closed, 403, 'This topic is closed.');

        $lastPosts = $topic->posts()
            ->orderBy('posted', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        return view('post.reply', compact('topic', 'lastPosts'));
    }

    public function storeReply(StorePostRequest $request, Topic $topic): RedirectResponse
    {
        abort_if($topic->closed, 403, 'This topic is closed.');

        $user = Auth::user();
        $now = now();
        $num = $topic->posts()->count() + 1;

        $post = $topic->posts()->create([
            'forum_id' => $topic->forum_id,
            'poster' => $user->username,
            'poster_id' => $user->id,
            'poster_ip' => $request->ip(),
            'posted' => $now,
            'message' => $request->input('message'),
            'num' => $num,
        ]);

        $topic->increment('num_replies');
        $topic->last_post = [
            'time' => $now->timestamp,
            'poster' => $user->username,
            'poster_id' => $user->id,
            'post_id' => $post->id,
            'num' => $num,
        ];
        $topic->save();

        $forum = $topic->forum;
        if ($forum) {
            $forum->increment('num_posts');
            $forum->last_post = [
                'time' => $now->timestamp,
                'poster' => $user->username,
                'post_id' => $post->id,
                'topic_id' => $topic->id,
                'subject' => $topic->subject,
                'num' => $num,
            ];
            $forum->save();
        }

        $user->increment('num_posts');
        $user->save();

        $lastPage = max(1, (int) ceil($num / 25));

        return redirect()->route('topic.show', ['topic' => $topic->id, 'page' => $lastPage])
            ->withFragment('p'.$num);
    }

    public function edit(Post $post): View
    {
        $user = Auth::user();

        abort_unless(
            $user->group_id <= 2 || $post->poster_id === $user->id,
            403,
            'You are not allowed to edit this post.',
        );

        return view('post.edit', compact('post'));
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(
            $user->group_id <= 2 || $post->poster_id === $user->id,
            403,
            'You are not allowed to edit this post.',
        );

        $post->message = $request->input('message');
        $post->edited = now();
        $post->edited_by = $user->username;
        $post->save();

        $page = max(1, (int) ceil(($post->num ?? 1) / 25));

        return redirect()->route('topic.show', ['topic' => $post->topic_id, 'page' => $page])
            ->withFragment('p'.($post->num ?? ''));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $user = Auth::user();

        abort_unless(
            $user->group_id <= 2 || $post->poster_id === $user->id,
            403,
            'You are not allowed to delete this post.',
        );

        $topic = $post->topic;
        $forum = $post->forum;

        $post->delete();

        if ($topic) {
            $remaining = $topic->posts()->count();
            if ($remaining === 0) {
                $topic->delete();
            } else {
                $topic->num_replies = max(0, $remaining - 1);
                $lastPost = $topic->posts()->orderBy('posted', 'desc')->first();
                if ($lastPost) {
                    $topic->last_post = [
                        'time' => $lastPost->posted->timestamp,
                        'poster' => $lastPost->poster,
                        'poster_id' => $lastPost->poster_id,
                        'post_id' => $lastPost->id,
                        'num' => $lastPost->num,
                    ];
                }
                $topic->save();
            }
        }

        if ($forum) {
            $forum->num_posts = max(0, ($forum->num_posts ?? 1) - 1);
            $forum->save();
        }

        return redirect()->route('forum.show', $forum?->id ?? $topic?->forum_id);
    }
}
