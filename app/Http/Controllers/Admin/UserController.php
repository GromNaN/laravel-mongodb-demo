<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'regexp', '/' . preg_quote($search, '/') . '/i')
                    ->orWhere('email', 'regexp', '/' . preg_quote($search, '/') . '/i');
            });
        }

        $users = $query->orderBy('username')->paginate(50)->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:2', 'max:25'],
            'email'    => ['required', 'email'],
            'group_id' => ['required', 'integer', 'min:1', 'max:5'],
            'title'    => ['nullable', 'string', 'max:50'],
        ]);

        $user->username = $request->input('username');
        $user->email    = $request->input('email');
        $user->group_id = (int) $request->input('group_id');
        $user->title    = $request->input('title');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Post::where('poster_id', (string) $user->_id)->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User and their posts deleted.');
    }
}
