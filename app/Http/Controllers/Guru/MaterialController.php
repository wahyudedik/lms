<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Constants\AuthorizationMessages;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Course $course)
    {
        // Check authorization using policy
        $this->authorize('view', $course);

        $materials = $course->materials()->with('creator')->ordered()->paginate(15);

        return view('guru.materials.index', compact('course', 'materials'));
    }

    public function create(Course $course)
    {
        // Check authorization using policy
        $this->authorize('view', $course);

        return view('guru.materials.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        // Check authorization using policy
        $this->authorize('update', $course);
        $this->authorize('create', Material::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,video,link,youtube',
            'file' => 'required_if:type,file,video|file|max:51200', // 50MB max
            'url' => 'required_if:type,link,youtube|url|nullable',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['course_id'] = $course->id;
        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('materials/' . $course->id, 'public');

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        Material::create($validated);

        return redirect()
            ->route('guru.courses.materials.index', $course)
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function show(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('view', $material);

        $material->load(['creator', 'comments.user', 'comments.replies.user']);

        return view('guru.materials.show', compact('course', 'material'));
    }

    public function edit(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('update', $material);

        return view('guru.materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('update', $material);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,video,link,youtube',
            'file' => 'nullable|file|max:51200', // 50MB max
            'url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        // Handle new file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($material->file_path && Storage::exists($material->file_path)) {
                Storage::delete($material->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('materials/' . $course->id, 'public');

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $material->update($validated);

        return redirect()
            ->route('guru.courses.materials.index', $course)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('delete', $material);

        $material->delete();

        return redirect()
            ->route('guru.courses.materials.index', $course)
            ->with('success', 'Materi berhasil dihapus!');
    }

    public function toggleStatus(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('update', $material);

        if ($material->is_published) {
            $material->unpublish();
            $message = 'Materi berhasil di-unpublish!';
        } else {
            $material->publish();
            $message = 'Materi berhasil dipublikasikan!';
        }

        return back()->with('success', $message);
    }

    public function reorder(Request $request, Course $course)
    {
        // Check authorization using policy
        $this->authorize('update', $course);

        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*' => 'required|exists:materials,id',
        ]);

        foreach ($validated['materials'] as $index => $materialId) {
            Material::where('id', $materialId)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
