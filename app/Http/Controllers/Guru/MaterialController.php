<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Constants\AuthorizationMessages;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Material;
use App\Services\GroupTargetedNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    use ResolvesRolePrefix;
    public function index(Course $course)
    {
        // Check authorization using policy
        $this->authorize('view', $course);

        $materials = $course->materials()->with(['creator', 'courseGroups'])->ordered()->paginate(15);

        return view('guru.materials.index', compact('course', 'materials'));
    }

    public function create(Course $course)
    {
        // Check authorization using policy
        $this->authorize('view', $course);

        $course->load('courseGroups');

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
            'file' => 'required_if:type,file,video|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,mp4,mp3,jpg,jpeg,png,gif,svg,webp|max:51200', // 50MB max
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

        $material = Material::create($validated);

        // Handle group associations
        if ($request->has('group_ids')) {
            $groupIds = array_filter((array) $request->input('group_ids'));

            if (!empty($groupIds)) {
                // Validate max 20 groups per material
                if (count($groupIds) > 20) {
                    $material->delete();
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Maksimal 20 kelompok per materi.']);
                }

                // Validate all group_ids belong to the same course
                $validCount = CourseGroup::whereIn('id', $groupIds)
                    ->where('course_id', $course->id)
                    ->count();

                if ($validCount !== count($groupIds)) {
                    $material->delete();
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Kelompok harus berasal dari kursus yang sama.']);
                }

                $material->courseGroups()->sync($groupIds);
            }
        }

        // Notify targeted students if material is published
        if ($material->is_published) {
            $notificationService = app(GroupTargetedNotificationService::class);
            $recipients = $notificationService->getRecipientsForMaterial($material);

            if ($recipients->isNotEmpty()) {
                foreach ($recipients->chunk(100) as $chunk) {
                    Notification::send($chunk, new \App\Notifications\MaterialPublished($material));
                }
            }
        }

        return redirect()
            ->to($this->teacherRoute('courses.materials.index', $course))
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function show(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('view', $material);

        $material->load(['creator', 'courseGroups', 'comments.user', 'comments.replies.user']);

        return view('guru.materials.show', compact('course', 'material'));
    }

    public function edit(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('update', $material);

        $course->load('courseGroups');
        $material->load('courseGroups');

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
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,mp4,mp3,jpg,jpeg,png,gif,svg,webp|max:51200', // 50MB max
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

        // Handle group associations
        if ($request->has('group_ids')) {
            $groupIds = array_filter((array) $request->input('group_ids'));

            if (empty($groupIds)) {
                // Empty array = remove all associations = ungrouped
                $material->courseGroups()->sync([]);
            } else {
                // Validate max 20 groups per material
                if (count($groupIds) > 20) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Maksimal 20 kelompok per materi.']);
                }

                // Validate all group_ids belong to the same course
                $validCount = CourseGroup::whereIn('id', $groupIds)
                    ->where('course_id', $course->id)
                    ->count();

                if ($validCount !== count($groupIds)) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Kelompok harus berasal dari kursus yang sama.']);
                }

                $material->courseGroups()->sync($groupIds);
            }
        }

        return redirect()
            ->to($this->teacherRoute('courses.materials.index', $course))
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy(Course $course, Material $material)
    {
        // Check authorization using policy
        $this->authorize('delete', $material);

        $material->delete();

        return redirect()
            ->to($this->teacherRoute('courses.materials.index', $course))
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

            // Notify targeted students that material is now published
            $notificationService = app(GroupTargetedNotificationService::class);
            $recipients = $notificationService->getRecipientsForMaterial($material);

            if ($recipients->isNotEmpty()) {
                foreach ($recipients->chunk(100) as $chunk) {
                    Notification::send($chunk, new \App\Notifications\MaterialPublished($material));
                }
            }

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
