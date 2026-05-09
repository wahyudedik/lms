<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBankCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionBankCategoryController extends Controller
{
    public function index()
    {
        $categories = QuestionBankCategory::with(['parent', 'children'])
            ->withCount('questions')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.question-bank-categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = QuestionBankCategory::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.question-bank-categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:7',
            'order'       => 'nullable|integer|min:0',
            'parent_id'   => 'nullable|exists:question_bank_categories,id',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['order']     = $validated['order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        QuestionBankCategory::create($validated);

        return redirect()
            ->route('admin.question-bank-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(QuestionBankCategory $questionBankCategory)
    {
        $parents = QuestionBankCategory::whereNull('parent_id')
            ->where('id', '!=', $questionBankCategory->id)
            ->orderBy('name')
            ->get();

        return view('admin.question-bank-categories.edit', compact('questionBankCategory', 'parents'));
    }

    public function update(Request $request, QuestionBankCategory $questionBankCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:7',
            'order'       => 'nullable|integer|min:0',
            'parent_id'   => 'nullable|exists:question_bank_categories,id',
            'is_active'   => 'boolean',
        ]);

        // Prevent setting itself as parent
        if (isset($validated['parent_id']) && (int) $validated['parent_id'] === $questionBankCategory->id) {
            $validated['parent_id'] = null;
        }

        $validated['order']     = $validated['order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $questionBankCategory->update($validated);

        return redirect()
            ->route('admin.question-bank-categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(QuestionBankCategory $questionBankCategory)
    {
        if ($questionBankCategory->questions()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki soal.');
        }

        if ($questionBankCategory->children()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki sub-kategori.');
        }

        $questionBankCategory->delete();

        return redirect()
            ->route('admin.question-bank-categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
