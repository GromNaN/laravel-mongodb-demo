<?php

namespace App\Providers;

use App\Models\Ban;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Group;
use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Rate limiters
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Route model binding
        Route::model('forum', Forum::class);
        Route::model('topic', Topic::class);
        Route::model('post', Post::class);
        Route::model('user', User::class);
        Route::model('ban', Ban::class);
        Route::model('report', Report::class);
        Route::model('group', Group::class);
        Route::model('cat', Category::class);

        // Authorization gates
        Gate::define('admin', function (User $user): bool {
            return $user->group_id === Group::ADMIN;
        });

        Gate::define('moderate', function (User $user): bool {
            return $user->group_id <= Group::MODERATOR;
        });

        Gate::define('post-reply', function (User $user): bool {
            return $user->group_id <= Group::MEMBER && ! $user->is_banned;
        });

        Gate::define('edit-post', function (User $user, Post $post): bool {
            if ($user->group_id === Group::ADMIN) {
                return true;
            }

            if ($user->group_id === Group::MODERATOR) {
                return true;
            }

            if ((string) $user->_id === (string) $post->poster_id) {
                $editWindow = 30 * 60; // 30 minutes in seconds
                $postAge = now()->diffInSeconds($post->posted);

                return $postAge <= $editWindow;
            }

            return false;
        });

        Gate::define('delete-post', function (User $user, Post $post): bool {
            if ($user->group_id <= Group::MODERATOR) {
                return true;
            }

            return (string) $user->_id === (string) $post->poster_id;
        });
    }
}
