<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $sortColumn = $request->input('sort', 'username');
        $sortOrder = $request->input('order', 'asc');

        $allowedSorts = ['username', 'num_posts', 'registered', 'last_post'];
        if (! in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'username';
        }

        $users = User::orderBy($sortColumn, $sortOrder === 'desc' ? 'desc' : 'asc')
            ->paginate(50);

        return view('user.index', compact('users', 'sortColumn', 'sortOrder'));
    }
}
