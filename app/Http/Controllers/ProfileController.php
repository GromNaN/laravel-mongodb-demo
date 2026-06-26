<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $recentPosts = Post::where('poster_id', (string) $user->_id)
            ->orderBy('posted', 'desc')
            ->limit(10)
            ->get();

        return view('profile.show', compact('user', 'recentPosts'));
    }

    public function edit(User $user): View
    {
        $auth = Auth::user();
        abort_unless($auth->group_id <= 2 || (string) $auth->id === (string) $user->id, 403);

        return view('profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request, User $user): RedirectResponse
    {
        $auth = Auth::user();
        abort_unless($auth->group_id <= 2 || (string) $auth->id === (string) $user->id, 403);

        $user->fill($request->only([
            'realname',
            'url',
            'email',
            'location',
            'signature',
        ]));

        if ($request->has('preferences')) {
            $user->preferences = array_merge(
                $user->preferences ?? [],
                $request->input('preferences', []),
            );
        }

        $user->save();

        return redirect()->route('profile.show', $user->id)->with('success', 'Profile updated.');
    }
}
