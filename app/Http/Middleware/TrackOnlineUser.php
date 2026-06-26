<?php

namespace App\Http\Middleware;

use App\Models\OnlineUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackOnlineUser
{
    public function handle(Request $request, Closure $next): mixed
    {
        $now = now();
        $idleThreshold = $now->copy()->subMinutes(5);

        if (Auth::check()) {
            $user = Auth::user();
            $existing = OnlineUser::where('user_id', (string) $user->_id)->first();

            $isIdle = $existing && $existing->logged && $existing->logged->lt($idleThreshold);

            OnlineUser::updateOrCreate(
                ['user_id' => (string) $user->_id],
                [
                    'ident'  => $user->username,
                    'logged' => $now,
                    'idle'   => $isIdle,
                ],
            );
        } else {
            $ip = $request->ip();
            $existing = OnlineUser::where('ident', $ip)->where('user_id', 1)->first();

            $isIdle = $existing && $existing->logged && $existing->logged->lt($idleThreshold);

            OnlineUser::updateOrCreate(
                ['ident' => $ip, 'user_id' => 1],
                [
                    'logged' => $now,
                    'idle'   => $isIdle,
                ],
            );
        }

        return $next($request);
    }
}
