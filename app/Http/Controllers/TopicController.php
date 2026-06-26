<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function show(Topic $topic): View
    {
        $topic->increment('num_views');

        $posts = $topic->posts()
            ->orderBy('posted', 'asc')
            ->paginate(25);

        return view('topic.show', compact('topic', 'posts'));
    }

    public function subscribe(Topic $topic): RedirectResponse
    {
        $user = Auth::user();
        $subscriptions = $user->topic_subscriptions ?? [];

        if (! in_array((string) $topic->_id, $subscriptions)) {
            $subscriptions[] = (string) $topic->_id;
            $user->topic_subscriptions = $subscriptions;
            $user->save();
        }

        return redirect()->back()->with('success', 'Subscribed to topic.');
    }

    public function unsubscribe(Topic $topic): RedirectResponse
    {
        $user = Auth::user();
        $subscriptions = array_filter(
            $user->topic_subscriptions ?? [],
            fn ($id) => $id !== (string) $topic->_id,
        );

        $user->topic_subscriptions = array_values($subscriptions);
        $user->save();

        return redirect()->back()->with('success', 'Unsubscribed from topic.');
    }

    public function newPost(Topic $topic): RedirectResponse
    {
        $user = Auth::user();
        $lastVisit = $user ? $user->last_visit : null;

        if ($lastVisit) {
            $firstUnread = Post::where('topic_id', (string) $topic->_id)
                ->where('posted', '>', $lastVisit)
                ->orderBy('posted', 'asc')
                ->first();

            if ($firstUnread) {
                $postsBeforeUnread = Post::where('topic_id', (string) $topic->_id)
                    ->where('posted', '<', $firstUnread->posted)
                    ->count();

                $page = (int) ceil(($postsBeforeUnread + 1) / 25);

                return redirect()->route('topic.show', ['topic' => $topic->_id, 'page' => $page])
                    ->withFragment('post-' . $firstUnread->_id);
            }
        }

        $postCount = $topic->posts()->count();
        $lastPage = max(1, (int) ceil($postCount / 25));

        return redirect()->route('topic.show', ['topic' => $topic->_id, 'page' => $lastPage]);
    }
}
