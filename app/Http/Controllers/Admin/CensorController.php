<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CensorController extends Controller
{
    private const CONFIG_KEY = 'censor_rules';

    public function index(): View
    {
        $rules = json_decode(ForumConfig::get(self::CONFIG_KEY, '[]'), true) ?? [];

        return view('admin.censoring.index', compact('rules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'search'      => ['required', 'string', 'max:60'],
            'replacement' => ['required', 'string', 'max:60'],
        ]);

        $rules = json_decode(ForumConfig::get(self::CONFIG_KEY, '[]'), true) ?? [];

        $rules[] = [
            'id'          => uniqid('censor_', true),
            'search'      => $request->input('search'),
            'replacement' => $request->input('replacement'),
        ];

        ForumConfig::set(self::CONFIG_KEY, json_encode($rules));

        return redirect()->route('admin.censoring.index')->with('success', 'Censor rule added.');
    }
}
