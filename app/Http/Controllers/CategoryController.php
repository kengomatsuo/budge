<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())
            ->withCount('expenses')
            ->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_default'] = false;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', __('messages.success'));
    }

    public function edit(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', __('messages.success'));
    }

    public function destroy(Category $category)
    {
        abort_if($category->user_id !== auth()->id() || $category->is_default, 403);
        $category->delete();
        return redirect()->route('categories.index')->with('success', __('messages.success'));
    }
}
