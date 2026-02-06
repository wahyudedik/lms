<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::query()
            ->withCount('users')
            ->orderByDesc('is_general')
            ->orderBy('education_level')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $educationLevels = SchoolClass::EDUCATION_LEVELS;
        $generalClassExists = SchoolClass::query()->where('is_general', true)->exists();

        return view('admin.classes.create', compact('educationLevels', 'generalClassExists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'education_level' => ['nullable', 'in:' . implode(',', array_keys(SchoolClass::EDUCATION_LEVELS))],
            'capacity' => ['required', 'integer', 'min:1'],
            'is_general' => ['nullable', 'boolean'],
        ]);

        $isGeneral = (bool) ($validated['is_general'] ?? false);

        if ($isGeneral && SchoolClass::query()->where('is_general', true)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['is_general' => __('General class already exists.')]);
        }

        if ($isGeneral) {
            $validated['education_level'] = null;
        }

        SchoolClass::create([
            'name' => $validated['name'],
            'education_level' => $validated['education_level'] ?? null,
            'capacity' => $validated['capacity'],
            'is_general' => $isGeneral,
        ]);

        return redirect()->route('admin.classes.index')->with('success', __('Class created successfully.'));
    }

    public function edit(SchoolClass $class)
    {
        $educationLevels = SchoolClass::EDUCATION_LEVELS;
        $generalClassExists = SchoolClass::query()
            ->where('is_general', true)
            ->where('id', '!=', $class->id)
            ->exists();

        return view('admin.classes.edit', compact('class', 'educationLevels', 'generalClassExists'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'education_level' => ['nullable', 'in:' . implode(',', array_keys(SchoolClass::EDUCATION_LEVELS))],
            'capacity' => ['required', 'integer', 'min:1'],
            'is_general' => ['nullable', 'boolean'],
        ]);

        $isGeneral = (bool) ($validated['is_general'] ?? false);

        if ($isGeneral && SchoolClass::query()->where('is_general', true)->where('id', '!=', $class->id)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['is_general' => __('General class already exists.')]);
        }

        if ($isGeneral) {
            $validated['education_level'] = null;
        }

        $class->update([
            'name' => $validated['name'],
            'education_level' => $validated['education_level'] ?? null,
            'capacity' => $validated['capacity'],
            'is_general' => $isGeneral,
        ]);

        return redirect()->route('admin.classes.index')->with('success', __('Class updated successfully.'));
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->is_general) {
            return back()->with('error', __('General class cannot be deleted.'));
        }

        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', __('Class deleted successfully.'));
    }
}

