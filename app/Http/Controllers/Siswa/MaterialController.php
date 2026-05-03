<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use App\Models\MaterialComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        // Get enrolled courses
        $enrolledCourses = Auth::user()->enrollments()
            ->with('course')
            ->get()
            ->pluck('course');

        // Build query
        $query = Material::with(['course.instructor'])
            ->whereIn('course_id', $enrolledCourses->pluck('id'))
            ->where('is_published', true)
            ->ordered();

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $materials = $query->paginate(12);

        // Statistics
        $stats = [
            'pdf' => Material::whereIn('course_id', $enrolledCourses->pluck('id'))
                ->where('is_published', true)
                ->where('type', 'pdf')
                ->count(),
            'video' => Material::whereIn('course_id', $enrolledCourses->pluck('id'))
                ->where('is_published', true)
                ->whereIn('type', ['video', 'youtube'])
                ->count(),
            'link' => Material::whereIn('course_id', $enrolledCourses->pluck('id'))
                ->where('is_published', true)
                ->where('type', 'link')
                ->count(),
        ];

        return view('siswa.materials.index', compact('materials', 'enrolledCourses', 'stats'));
    }

    public function show(Material $material)
    {
        // Check if user is enrolled in the course
        $enrollment = Auth::user()->enrollments()
            ->where('course_id', $material->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda belum terdaftar di kursus ini.');
        }

        // Check if material is published
        if (!$material->is_published) {
            abort(404, 'Materi tidak ditemukan.');
        }

        // Load relationships
        $material->load(['course.instructor', 'comments.user']);

        return view('siswa.materials.show', compact('material'));
    }

    public function comment(Request $request, Material $material)
    {
        // Check if user is enrolled in the course
        $enrollment = Auth::user()->enrollments()
            ->where('course_id', $material->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda belum terdaftar di kursus ini.');
        }

        // Check if comments are allowed
        if (!$material->allow_comments) {
            return back()->with('error', 'Komentar tidak diizinkan untuk materi ini.');
        }

        // Validate
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Create comment
        MaterialComment::create([
            'material_id' => $material->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
