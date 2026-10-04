<?php

namespace App\Http\Controllers;

use App\Exports\MaterialAttendanceExport;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\MaterialAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class MaterialAttendanceController extends Controller
{
    /**
     * Form absensi per materi per tanggal.
     */
    public function index(Request $request, Course $course, Material $material)
    {
        $this->authorize('manage', [MaterialAttendance::class, $material]);

        abort_if($material->course_id !== $course->id, 404);

        $data = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $date = $data['date'] ?? today()->format('Y-m-d');

        $students = $this->studentsForAttendance($course, $material, $date);

        return view('attendance.index', [
            'course' => $course,
            'material' => $material,
            'date' => $date,
            'students' => $students,
        ]);
    }

    /**
     * Simpan absensi massal per materi per tanggal.
     */
    public function save(Request $request, Course $course, Material $material)
    {
        $this->authorize('manage', [MaterialAttendance::class, $material]);

        abort_if($material->course_id !== $course->id, 404);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.user_id' => ['required', 'integer'],
            'records.*.status' => ['required', 'in:hadir,sakit,ijin,keluar'],
            'records.*.alasan' => ['nullable', 'string', 'max:500'],
        ]);

        // Cegah absensi silang kursus: setiap user_id wajib siswa berhak
        // (enrollment aktif + visibility materi).
        $eligibleIds = $this->eligibleStudentIds($course, $material);
        $invalidIds = collect($data['records'])->pluck('user_id')->diff($eligibleIds);

        if ($invalidIds->isNotEmpty()) {
            throw ValidationException::withMessages([
                'records' => 'Terdapat siswa yang tidak berhak diabsen pada kursus/materi ini.',
            ]);
        }

        $rows = collect($data['records'])->map(fn (array $record) => [
            'user_id' => (int) $record['user_id'],
            'status' => $record['status'],
            'alasan' => $record['alasan'] ?? null,
        ])->all();

        $stats = MaterialAttendance::upsertBulk(
            $material->id,
            $data['date'],
            $rows,
            $request->user()->id
        );

        return redirect()
            ->back()
            ->with('success', sprintf(
                'Absensi berhasil disimpan: %d data baru, %d data diperbarui.',
                $stats['created'],
                $stats['updated']
            ));
    }

    /**
     * Laporan absensi per kursus (admin ATAU guru/dosen pemilik course).
     */
    public function report(Request $request, Course $course)
    {
        $this->authorizeCourseReport($course);

        $filters = $this->validateReportFilters($request);

        $attendances = $this->reportQuery($course, $filters)->get();

        $summary = [
            'hadir' => $attendances->where('status', MaterialAttendance::STATUS_HADIR)->count(),
            'sakit' => $attendances->where('status', MaterialAttendance::STATUS_SAKIT)->count(),
            'ijin' => $attendances->where('status', MaterialAttendance::STATUS_IJIN)->count(),
            'keluar' => $attendances->where('status', MaterialAttendance::STATUS_KELUAR)->count(),
            'total' => $attendances->count(),
        ];

        $materials = Material::where('course_id', $course->id)
            ->orderBy('order')
            ->get();

        return view('attendance.report', [
            'course' => $course,
            'materials' => $materials,
            'filters' => $filters,
            'attendances' => $attendances->groupBy('material_id'),
            'summary' => $summary,
        ]);
    }

    /**
     * Export absensi ke Excel (authorization sama dengan report).
     */
    public function exportExcel(Request $request, Course $course)
    {
        $this->authorizeCourseReport($course);

        $filters = $this->validateReportFilters($request);

        $attendances = $this->reportQuery($course, $filters)->get();

        $filename = sprintf(
            'absensi_%s_%s.xlsx',
            Str::slug($course->title),
            now()->format('Ymd_His')
        );

        return Excel::download(
            new MaterialAttendanceExport($attendances, $course),
            $filename
        );
    }

    /**
     * Daftar absensi milik siswa yang login (siswa/mahasiswa).
     */
    public function myAttendance(Request $request)
    {
        $this->authorize('viewAny', MaterialAttendance::class);

        $data = $request->validate([
            'course_id' => ['nullable', 'integer'],
        ]);

        $query = MaterialAttendance::byStudent($request->user()->id)
            ->with('material.course')
            ->orderByDesc('material_attendances.attendance_date')
            ->orderByDesc('material_attendances.id');

        if (! empty($data['course_id'])) {
            $courseId = (int) $data['course_id'];

            abort_unless(
                Enrollment::where('user_id', $request->user()->id)
                    ->where('course_id', $courseId)
                    ->exists(),
                404,
                'Kursus tidak ditemukan.'
            );

            $query->forCourse($courseId);
        }

        $attendances = $query->get();

        $courses = $request->user()->enrollments()
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter()
            ->values();

        return view('siswa.attendance.index', [
            'attendances' => $attendances,
            'courses' => $courses,
            'filters' => $data,
        ]);
    }

    /**
     * ID siswa yang berhak diabsen: enrollment aktif di course,
     * lalu difilter visibility materi (jika materi di-target ke course group,
     * hanya member dari group tersebut).
     */
    protected function eligibleStudentIds(Course $course, Material $material): array
    {
        $activeIds = Enrollment::query()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->pluck('user_id');

        if (! $material->courseGroups()->exists()) {
            return $activeIds->values()->all();
        }

        $memberIds = $material->courseGroups()
            ->with('members:id')
            ->get()
            ->pluck('members')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->values();

        return $activeIds->intersect($memberIds)->values()->all();
    }

    /**
     * Susun data siswa yang bisa diabsen + absensi yang sudah ada untuk tanggal tertentu.
     *
     * @return Collection<int, array{user: User, attendance: MaterialAttendance|null}>
     */
    protected function studentsForAttendance(Course $course, Material $material, string $date): Collection
    {
        $eligibleIds = $this->eligibleStudentIds($course, $material);

        $users = User::whereIn('id', $eligibleIds)
            ->orderBy('name')
            ->get();

        $existing = MaterialAttendance::forMaterial($material->id)
            ->forDate($date)
            ->get()
            ->keyBy('user_id');

        return $users->map(fn (User $user) => [
            'user' => $user,
            'attendance' => $existing->get($user->id),
        ])->values();
    }

    /**
     * Authorize akses laporan/export: admin ATAU guru/dosen pemilik course
     * (pola abort_unless manual, konsisten dengan Guru\ReportController).
     */
    protected function authorizeCourseReport(Course $course): void
    {
        $user = request()->user();

        abort_unless(
            $user->isAdmin()
            || (($user->isGuru() || $user->isDosen()) && $course->instructor_id === $user->id),
            403,
            'Anda tidak memiliki akses ke laporan absensi kursus ini.'
        );
    }

    /**
     * Validasi filter laporan/export.
     *
     * @return array{material_id: int|null, date_from: string|null, date_to: string|null}
     */
    protected function validateReportFilters(Request $request): array
    {
        $data = $request->validate([
            'material_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return [
            'material_id' => isset($data['material_id']) ? (int) $data['material_id'] : null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
        ];
    }

    /**
     * Query absensi untuk laporan/export.
     *
     * @param  array{material_id: int|null, date_from: string|null, date_to: string|null}  $filters
     */
    protected function reportQuery(Course $course, array $filters)
    {
        $query = MaterialAttendance::forCourse($course->id)
            ->with(['material', 'student', 'recorder'])
            ->orderBy('material_attendances.attendance_date')
            ->orderBy('material_attendances.id');

        if ($filters['material_id']) {
            $query->forMaterial($filters['material_id']);
        }

        if ($filters['date_from'] && $filters['date_to']) {
            $query->forDateRange($filters['date_from'], $filters['date_to']);
        } elseif ($filters['date_from']) {
            $query->where('material_attendances.attendance_date', '>=', $filters['date_from']);
        } elseif ($filters['date_to']) {
            $query->where('material_attendances.attendance_date', '<=', $filters['date_to']);
        }

        return $query;
    }
}
