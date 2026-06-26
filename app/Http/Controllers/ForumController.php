<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function show(Forum $forum): View
    {
        $topics = $forum->topics()
            ->orderByDesc('sticky')
            ->orderByDesc('last_post')
            ->paginate(25);

        return view('forum.show', compact('forum', 'topics'));
    }

    public function subscribe(Forum $forum): RedirectResponse
    {
        $user = Auth::user();
        $subscriptions = $user->forum_subscriptions ?? [];

        if (! in_array((string) $forum->_id, $subscriptions)) {
            $subscriptions[] = (string) $forum->_id;
            $user->forum_subscriptions = $subscriptions;
            $user->save();
        }

        return redirect()->back()->with('success', 'Subscribed to forum.');
    }

    public function unsubscribe(Forum $forum): RedirectResponse
    {
        $user = Auth::user();
        $subscriptions = array_filter(
            $user->forum_subscriptions ?? [],
            fn ($id) => $id !== (string) $forum->_id,
        );

        $user->forum_subscriptions = array_values($subscriptions);
        $user->save();

        return redirect()->back()->with('success', 'Unsubscribed from forum.');
    }

    public function markRead(Request $request, Forum $forum): RedirectResponse
    {
        $request->session()->put('forum_read_' . $forum->_id, now()->timestamp);

        return redirect()->back();
    }
}
