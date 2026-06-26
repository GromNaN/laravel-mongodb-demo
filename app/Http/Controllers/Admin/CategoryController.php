<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('position')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:80'],
            'position' => ['nullable', 'integer'],
        ]);

        Category::create([
            'name'     => $request->input('name'),
            'position' => (int) $request->input('position', 0),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, Category $cat): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:80'],
            'position' => ['nullable', 'integer'],
        ]);

        $cat->name     = $request->input('name');
        $cat->position = (int) $request->input('position', 0);
        $cat->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function moveup(Category $cat): RedirectResponse
    {
        $cat->decrement('position');

        return redirect()->route('admin.categories.index');
    }

    public function movedown(Category $cat): RedirectResponse
    {
        $cat->increment('position');

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $cat): RedirectResponse
    {
        $cat->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
