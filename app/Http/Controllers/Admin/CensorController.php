<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CensorController extends Controller
{
    public function index(): View
    {
        $rules = ForumConfig::instance()->censor_rules ?? [];

        return view('admin.censoring.index', compact('rules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'search'      => ['required', 'string', 'max:60'],
            'replacement' => ['required', 'string', 'max:60'],
        ]);

        $config = ForumConfig::instance();
        $rules = $config->censor_rules ?? [];

        $rules[] = [
            'id'          => uniqid('censor_', true),
            'search'      => $request->input('search'),
            'replacement' => $request->input('replacement'),
        ];

        $config->censor_rules = $rules;
        $config->save();

        return redirect()->route('admin.censoring.index')->with('success', 'Censor rule added.');
    }
}
