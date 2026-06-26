<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BanController extends Controller
{
    public function index(): View
    {
        $bans = Ban::orderBy('created_at', 'desc')->paginate(50);

        return view('admin.bans.index', compact('bans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username'   => ['nullable', 'string', 'max:25'],
            'ip'         => ['nullable', 'ip'],
            'email'      => ['nullable', 'email'],
            'message'    => ['nullable', 'string', 'max:255'],
            'expiration' => ['nullable', 'date'],
        ]);

        Ban::create([
            'username'    => $request->input('username'),
            'ip'          => $request->input('ip'),
            'email'       => $request->input('email'),
            'message'     => $request->input('message'),
            'expiration'  => $request->input('expiration') ? \Carbon\Carbon::parse($request->input('expiration')) : null,
            'ban_creator' => \Auth::id(),
        ]);

        return redirect()->route('admin.bans.index')->with('success', 'Ban created.');
    }

    public function destroy(Ban $ban): RedirectResponse
    {
        $ban->delete();

        return redirect()->route('admin.bans.index')->with('success', 'Ban removed.');
    }
}
