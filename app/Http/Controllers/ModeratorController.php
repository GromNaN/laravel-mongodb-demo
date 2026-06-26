<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ModeratorController extends Controller
{
    private function authorizeModerator(Topic $topic): void
    {
        $user = Auth::user();
        $forum = Forum::find($topic->forum_id);
        $isModerator = $forum && in_array($user->username, $forum->moderators ?? []);

        abort_unless(Gate::allows('moderate') || $isModerator, 403);
    }

    public function closeTopic(Topic $topic): RedirectResponse
    {
        $this->authorizeModerator($topic);
        $topic->closed = true;
        $topic->save();

        return redirect()->back()->with('success', 'Topic closed.');
    }

    public function openTopic(Topic $topic): RedirectResponse
    {
        $this->authorizeModerator($topic);
        $topic->closed = false;
        $topic->save();

        return redirect()->back()->with('success', 'Topic opened.');
    }

    public function stickTopic(Topic $topic): RedirectResponse
    {
        $this->authorizeModerator($topic);
        $topic->sticky = true;
        $topic->save();

        return redirect()->back()->with('success', 'Topic stickied.');
    }

    public function unstickTopic(Topic $topic): RedirectResponse
    {
        $this->authorizeModerator($topic);
        $topic->sticky = false;
        $topic->save();

        return redirect()->back()->with('success', 'Topic unstickied.');
    }

    public function moveTopic(Request $request, Topic $topic): RedirectResponse
    {
        $this->authorizeModerator($topic);
        $request->validate([
            'forum_id' => ['required', 'string'],
        ]);

        $newForumId = $request->input('forum_id');
        $oldForumId = $topic->forum_id;

        $newForum = Forum::findOrFail($newForumId);

        $oldForum = Forum::find($oldForumId);
        if ($oldForum) {
            $oldForum->num_topics = max(0, ($oldForum->num_topics ?? 1) - 1);
            $postCount = $topic->posts()->count();
            $oldForum->num_posts = max(0, ($oldForum->num_posts ?? $postCount) - $postCount);
            $oldForum->save();
        }

        $topic->forum_id = $newForumId;
        $topic->posts()->each(function (Post $post) use ($newForumId) {
            $post->forum_id = $newForumId;
            $post->save();
        });
        $topic->save();

        $newForum->increment('num_topics');
        $postCount = $topic->posts()->count();
        $newForum->num_posts = ($newForum->num_posts ?? 0) + $postCount;
        $newForum->save();

        return redirect()->route('topic.show', $topic->_id)->with('success', 'Topic moved.');
    }

    public function deleteTopic(Topic $topic): RedirectResponse
    {
        $forumId = $topic->forum_id;
        $postCount = $topic->posts()->count();

        Post::where('topic_id', (string) $topic->_id)->delete();
        $topic->delete();

        $forum = Forum::find($forumId);
        if ($forum) {
            $forum->num_topics = max(0, ($forum->num_topics ?? 1) - 1);
            $forum->num_posts = max(0, ($forum->num_posts ?? $postCount) - $postCount);
            $forum->save();
        }

        return redirect()->route('forum.show', $forumId)->with('success', 'Topic deleted.');
    }

    public function reportPost(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        Report::create([
            'post_id'     => (string) $post->_id,
            'topic_id'    => (string) $post->topic_id,
            'forum_id'    => (string) $post->forum_id,
            'reported_by' => $user ? $user->username : 'Guest',
            'created'     => now(),
            'message'     => $request->input('message'),
        ]);

        return redirect()->back()->with('success', 'Post reported. Thank you.');
    }
}
