<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumCategoryController extends Controller
{
    /**
     * Display all categories.
     */
    public function index()
    {
        $categories = ForumCategory::with('creator')
            ->withCount('threads')
            ->ordered()
            ->get();

        return view('admin.forum-categories.index', compact('categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.forum-categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ForumCategory::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'created_by' => auth()->id(),
            'icon' => $validated['icon'] ?? 'fas fa-comments',
            'color' => $validated['color'] ?? '#3B82F6',
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(ForumCategory $forumCategory)
    {
        return view('admin.forum-categories.edit', compact('forumCategory'));
    }

    /**
     * Update a category.
     */
    public function update(Request $request, ForumCategory $forumCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $forumCategory->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroy(ForumCategory $forumCategory)
    {
        $forumCategory->delete();

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
