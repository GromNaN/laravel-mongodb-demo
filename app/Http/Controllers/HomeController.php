<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ForumConfig;
use App\Models\OnlineUser;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('forums')->orderBy('position')->get();
        $config = ForumConfig::instance();
        $onlineUsers = OnlineUser::where('user_id', '!=', 1)
            ->where('logged', '>=', now()->subMinutes(15))
            ->orderBy('logged', 'desc')
            ->get();
        $guestCount = OnlineUser::where('user_id', 1)
            ->where('logged', '>=', now()->subMinutes(15))
            ->count();

        $stats = [
            'total_posts'  => Post::count(),
            'total_topics' => Topic::count(),
            'total_users'  => User::count(),
            'newest_user'  => User::orderBy('registered', 'desc')->first(),
        ];

        return view('home.index', compact('categories', 'config', 'onlineUsers', 'guestCount', 'stats'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->session()->put('last_mark_read', now());

        return redirect()->back();
    }
}
