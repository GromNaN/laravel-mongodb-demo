<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnlineUser;
use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_topics' => Topic::count(),
            'total_posts' => Post::count(),
            'online_count' => OnlineUser::count(),
        ];
        $pendingReportsCount = Report::pending()->count();
        $latestUsers = User::orderBy('registered', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingReportsCount', 'latestUsers'));
    }
}
