<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('forums')->orderBy('position')->get();

        return view('admin.forums.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
            'position'    => ['nullable', 'integer'],
        ]);

        Forum::create([
            'name'        => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'description' => $request->input('description'),
            'position'    => (int) $request->input('position', 0),
        ]);

        return redirect()->route('admin.forums.index')->with('success', 'Forum created.');
    }

    public function edit(Forum $forum): View
    {
        $categories = Category::orderBy('position')->get();

        return view('admin.forums.edit', compact('forum', 'categories'));
    }

    public function update(Request $request, Forum $forum): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
            'position'    => ['nullable', 'integer'],
        ]);

        $forum->name        = $request->input('name');
        $forum->category_id = $request->input('category_id');
        $forum->description = $request->input('description');
        $forum->position    = (int) $request->input('position', 0);
        $forum->save();

        return redirect()->route('admin.forums.index')->with('success', 'Forum updated.');
    }

    public function moveup(Forum $forum): RedirectResponse
    {
        $forum->decrement('disp_position');

        return redirect()->route('admin.forums.index');
    }

    public function movedown(Forum $forum): RedirectResponse
    {
        $forum->increment('disp_position');

        return redirect()->route('admin.forums.index');
    }

    public function destroy(Forum $forum): RedirectResponse
    {
        $forum->delete();

        return redirect()->route('admin.forums.index')->with('success', 'Forum deleted.');
    }
}
