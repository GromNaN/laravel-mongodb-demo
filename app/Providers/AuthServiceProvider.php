<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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

            if ((string) $post->poster_id === (string) $user->_id) {
                $editWindow = 30 * 60; // 30 minutes in seconds
                $postAge = now()->diffInSeconds($post->posted);

                return $postAge <= $editWindow;
            }

            return false;
        });

        Gate::define('delete-post', function (User $user, Post $post): bool {
            if ($user->group_id === Group::ADMIN) {
                return true;
            }

            if ($user->group_id === Group::MODERATOR) {
                return true;
            }

            return (string) $post->poster_id === (string) $user->_id;
        });
    }
}
