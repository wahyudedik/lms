<?php

use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\MaterialAttendance;
use App\Models\User;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Feature Tests: Fitur Absensi (Material Attendance)
|--------------------------------------------------------------------------
|
| Backend-only tests: migration, model, policy, controller, routes, export.
| View Blade asli dibuat oleh subtask frontend; di sini view stub disediakan
| lewat location tambahan (resources/views tetap diprioritaskan bila sudah ada).
|
*/

beforeEach(function () {
    $base = sys_get_temp_dir().'/lms_attendance_stub_views';

    foreach ([
        'attendance/index.blade.php',
        'attendance/report.blade.php',
        'siswa/attendance/index.blade.php',
    ] as $view) {
        $path = $base.'/'.$view;
        File::ensureDirectoryExists(dirname($path));
        File::put($path, '<html><body>stub attendance view</body></html>');
    }

    view()->addLocation($base);
});

/**
 * Skenario dasar: guru pemilik course + 1 materi + 1 siswa aktif terdaftar.
 *
 * @return array{guru: User, course: Course, material: Material, siswa: User}
 */
function attendanceScenario(): array
{
    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    $material = Material::factory()->create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
    ]);
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    return compact('guru', 'course', 'material', 'siswa');
}

it('admin dapat membuka form absensi dan menyimpan data per tanggal dengan unik', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa] = attendanceScenario();
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(
        route('admin.attendance.index', [$course, $material])
    );

    $response->assertOk();
    $response->assertViewIs('attendance.index');
    $response->assertViewHas('course', fn ($c) => $c->id === $course->id);
    $response->assertViewHas('material', fn ($m) => $m->id === $material->id);
    $response->assertViewHas('date', today()->format('Y-m-d'));
    $response->assertViewHas('students', function ($students) use ($siswa) {
        return $students->count() === 1
            && $students->first()['user']->id === $siswa->id
            && $students->first()['attendance'] === null;
    });

    // Simpan absensi
    $this->actingAs($admin)->post(route('admin.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'sakit', 'alasan' => 'Demam'],
        ],
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $attendance = MaterialAttendance::where('material_id', $material->id)
        ->where('user_id', $siswa->id)
        ->whereDate('attendance_date', '2026-10-04')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBe('sakit')
        ->and($attendance->alasan)->toBe('Demam')
        ->and($attendance->recorded_by)->toBe($admin->id);

    // Simpan lagi tanggal sama → update, bukan duplikat
    $this->actingAs($admin)->post(route('admin.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertRedirect();

    expect(MaterialAttendance::where('material_id', $material->id)
        ->where('user_id', $siswa->id)
        ->count())->toBe(1);

    $attendance->refresh();
    expect($attendance->status)->toBe('hadir')
        ->and($attendance->alasan)->toBeNull();
});

it('guru pemilik course dapat mengelola absensi, guru lain ditolak 403', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    // Guru pemilik: index 200 + save berhasil
    $this->actingAs($guru)
        ->get(route('guru.attendance.index', [$course, $material]))
        ->assertOk();

    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-05',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(MaterialAttendance::where('material_id', $material->id)->count())->toBe(1);

    // Guru lain (bukan pemilik course) → 403 pada index & save
    $guruLain = User::factory()->create(['role' => 'guru', 'is_active' => true]);

    $this->actingAs($guruLain)
        ->get(route('guru.attendance.index', [$course, $material]))
        ->assertForbidden();

    $this->actingAs($guruLain)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-05',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertForbidden();
});

it('dosen dapat mengelola absensi kursus yang diajar (equivalence guru)', function () {
    $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $dosen->id]);
    $material = Material::factory()->create([
        'course_id' => $course->id,
        'created_by' => $dosen->id,
    ]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $mahasiswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    // Route dosen dapat diakses oleh role dosen
    $this->actingAs($dosen)
        ->get(route('dosen.attendance.index', [$course, $material]))
        ->assertOk()
        ->assertViewIs('attendance.index');

    $this->actingAs($dosen)->post(route('dosen.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $mahasiswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(MaterialAttendance::where('material_id', $material->id)
        ->where('user_id', $mahasiswa->id)
        ->count())->toBe(1);
});

it('siswa tidak dapat mengelola absensi tapi myAttendance hanya menampilkan data miliknya', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa] = attendanceScenario();

    $siswaLain = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $siswaLain->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    MaterialAttendance::create([
        'material_id' => $material->id,
        'user_id' => $siswa->id,
        'attendance_date' => '2026-10-04',
        'status' => 'hadir',
        'recorded_by' => $course->instructor_id,
    ]);
    MaterialAttendance::create([
        'material_id' => $material->id,
        'user_id' => $siswaLain->id,
        'attendance_date' => '2026-10-04',
        'status' => 'sakit',
        'recorded_by' => $course->instructor_id,
    ]);

    // Siswa ditolak pada endpoint manage (middleware role guru/admin/dosen)
    $this->actingAs($siswa)
        ->get(route('admin.attendance.index', [$course, $material]))
        ->assertForbidden();

    $this->actingAs($siswa)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertForbidden();

    // myAttendance: hanya data miliknya
    $response = $this->actingAs($siswa)->get(route('siswa.attendance.index'));

    $response->assertOk();
    $response->assertViewIs('siswa.attendance.index');
    $response->assertViewHas('attendances', function ($attendances) use ($siswa) {
        return $attendances->count() === 1
            && $attendances->first()->user_id === $siswa->id;
    });
    $response->assertViewHas('courses', fn ($courses) => $courses->count() === 1);
});

it('myAttendance dapat difilter per course yang di-enroll', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    $courseLain = Course::factory()->create(['instructor_id' => $guru->id]);
    $materialLain = Material::factory()->create([
        'course_id' => $courseLain->id,
        'created_by' => $guru->id,
    ]);
    Enrollment::factory()->create([
        'user_id' => $siswa->id,
        'course_id' => $courseLain->id,
        'status' => 'active',
    ]);

    MaterialAttendance::create([
        'material_id' => $material->id,
        'user_id' => $siswa->id,
        'attendance_date' => '2026-10-01',
        'status' => 'hadir',
    ]);
    MaterialAttendance::create([
        'material_id' => $materialLain->id,
        'user_id' => $siswa->id,
        'attendance_date' => '2026-10-02',
        'status' => 'sakit',
    ]);

    // Tanpa filter → semua data miliknya, terbaru dulu
    $this->actingAs($siswa)->get(route('siswa.attendance.index'))
        ->assertOk()
        ->assertViewHas('attendances', function ($attendances) use ($materialLain) {
            return $attendances->count() === 2
                && $attendances->first()->material_id === $materialLain->id;
        });

    // Filter course yang di-enroll
    $this->actingAs($siswa)->get(route('siswa.attendance.index', ['course_id' => $course->id]))
        ->assertOk()
        ->assertViewHas('attendances', function ($attendances) use ($material) {
            return $attendances->count() === 1
                && $attendances->first()->material_id === $material->id;
        });

    // Filter course yang bukan enrollment → 404
    $courseBukan = Course::factory()->create();
    $this->actingAs($siswa)->get(route('siswa.attendance.index', ['course_id' => $courseBukan->id]))
        ->assertNotFound();
});

it('menolak status tidak valid dan user_id yang bukan siswa berhak', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    // Status tidak valid
    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'libur', 'alasan' => null],
        ],
    ])->assertSessionHasErrors('records.0.status');

    // user_id bukan enrollment aktif (status dropped)
    $siswaDrop = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $siswaDrop->id,
        'course_id' => $course->id,
        'status' => 'dropped',
    ]);

    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswaDrop->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertSessionHasErrors('records');

    // user_id dari course lain (absensi silang kursus)
    $siswaLainCourse = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $courseLain = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::factory()->create([
        'user_id' => $siswaLainCourse->id,
        'course_id' => $courseLain->id,
        'status' => 'active',
    ]);

    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswaLainCourse->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertSessionHasErrors('records');

    expect(MaterialAttendance::count())->toBe(0);
});

it('materi yang ditarget ke course group hanya dapat diabsen untuk member group', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    $siswaLain = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Enrollment::factory()->create([
        'user_id' => $siswaLain->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $group = CourseGroup::factory()->create(['course_id' => $course->id, 'name' => 'Kelompok A']);
    $group->members()->attach($siswa->id);
    $material->courseGroups()->attach($group->id);

    // Index hanya menampilkan member group
    $this->actingAs($guru)
        ->get(route('guru.attendance.index', [$course, $material]))
        ->assertOk()
        ->assertViewHas('students', function ($students) use ($siswa) {
            return $students->count() === 1
                && $students->first()['user']->id === $siswa->id;
        });

    // Simpan untuk non-member → ditolak
    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswaLain->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertSessionHasErrors('records');

    // Simpan untuk member → berhasil
    $this->actingAs($guru)->post(route('guru.attendance.save', [$course, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
        ],
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(MaterialAttendance::where('user_id', $siswa->id)->count())->toBe(1)
        ->and(MaterialAttendance::where('user_id', $siswaLain->id)->count())->toBe(0);
});

it('report absensi ter-group per materi dengan filter tanggal dan ringkasan yang benar', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    $material2 = Material::factory()->create([
        'course_id' => $course->id,
        'created_by' => $guru->id,
    ]);

    MaterialAttendance::create(['material_id' => $material->id, 'user_id' => $siswa->id, 'attendance_date' => '2026-10-01', 'status' => 'hadir', 'recorded_by' => $guru->id]);
    MaterialAttendance::create(['material_id' => $material->id, 'user_id' => $siswa->id, 'attendance_date' => '2026-10-02', 'status' => 'hadir', 'recorded_by' => $guru->id]);
    MaterialAttendance::create(['material_id' => $material->id, 'user_id' => $siswa->id, 'attendance_date' => '2026-10-03', 'status' => 'sakit', 'alasan' => 'Flu', 'recorded_by' => $guru->id]);
    MaterialAttendance::create(['material_id' => $material2->id, 'user_id' => $siswa->id, 'attendance_date' => '2026-10-05', 'status' => 'ijin', 'recorded_by' => $guru->id]);
    MaterialAttendance::create(['material_id' => $material2->id, 'user_id' => $siswa->id, 'attendance_date' => '2026-10-06', 'status' => 'keluar', 'recorded_by' => $guru->id]);

    $response = $this->actingAs($guru)->get(route('guru.attendance.report', $course));

    $response->assertOk();
    $response->assertViewIs('attendance.report');
    $response->assertViewHas('course', fn ($c) => $c->id === $course->id);
    $response->assertViewHas('materials', fn ($m) => $m->count() === 2);
    $response->assertViewHas('summary', function ($summary) {
        return $summary['hadir'] === 2
            && $summary['sakit'] === 1
            && $summary['ijin'] === 1
            && $summary['keluar'] === 1
            && $summary['total'] === 5;
    });
    $response->assertViewHas('attendances', function ($grouped) use ($material, $material2) {
        return $grouped->count() === 2
            && $grouped->get($material->id)->count() === 3
            && $grouped->get($material2->id)->count() === 2;
    });

    // Filter tanggal bekerja
    $response = $this->actingAs($guru)->get(route('guru.attendance.report', [
        $course,
        'date_from' => '2026-10-01',
        'date_to' => '2026-10-02',
    ]));
    $response->assertOk();
    $response->assertViewHas('attendances', fn ($grouped) => $grouped->flatten()->count() === 2);
    $response->assertViewHas('summary', fn ($summary) => $summary['total'] === 2);

    // Filter materi bekerja
    $response = $this->actingAs($guru)->get(route('guru.attendance.report', [
        $course,
        'material_id' => $material2->id,
    ]));
    $response->assertOk();
    $response->assertViewHas('attendances', function ($grouped) use ($material2) {
        return $grouped->count() === 1
            && $grouped->get($material2->id)->count() === 2;
    });

    // Guru non-pemilik → 403
    $guruLain = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $this->actingAs($guruLain)->get(route('guru.attendance.report', $course))->assertForbidden();

    // Siswa → 403 (middleware role)
    $this->actingAs($siswa)->get(route('guru.attendance.report', $course))->assertForbidden();
});

it('export excel absensi berjalan untuk admin dan guru pemilik', function () {
    ['course' => $course, 'material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    MaterialAttendance::create([
        'material_id' => $material->id,
        'user_id' => $siswa->id,
        'attendance_date' => '2026-10-04',
        'status' => 'hadir',
        'recorded_by' => $guru->id,
    ]);

    $response = $this->actingAs($guru)->get(route('guru.attendance.export', $course));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))->toContain('absensi_');

    // Admin juga bisa export
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $this->actingAs($admin)->get(route('admin.attendance.export', $course))->assertOk();

    // Guru non-pemilik → 403
    $guruLain = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $this->actingAs($guruLain)->get(route('guru.attendance.export', $course))->assertForbidden();
});

it('policy material attendance: manage hanya admin/instruktur, view untuk data sendiri', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $guru = User::factory()->create(['role' => 'guru']);
    $guruLain = User::factory()->create(['role' => 'guru']);
    $dosen = User::factory()->create(['role' => 'dosen']);
    $siswa = User::factory()->create(['role' => 'siswa']);

    $course = Course::factory()->create(['instructor_id' => $dosen->id]);
    $material = Material::factory()->create(['course_id' => $course->id]);

    $attendance = MaterialAttendance::create([
        'material_id' => $material->id,
        'user_id' => $siswa->id,
        'attendance_date' => '2026-10-04',
        'status' => 'ijin',
    ]);

    // manage: admin & instruktur (dosen≡guru) saja
    expect($admin->can('manage', [MaterialAttendance::class, $material]))->toBeTrue()
        ->and($dosen->can('manage', [MaterialAttendance::class, $material]))->toBeTrue()
        ->and($guru->can('manage', [MaterialAttendance::class, $material]))->toBeFalse()
        ->and($guruLain->can('manage', [MaterialAttendance::class, $material]))->toBeFalse()
        ->and($siswa->can('manage', [MaterialAttendance::class, $material]))->toBeFalse()
        // view: pemilik data, instruktur, admin
        ->and($siswa->can('view', $attendance))->toBeTrue()
        ->and($dosen->can('view', $attendance))->toBeTrue()
        ->and($guruLain->can('view', $attendance))->toBeFalse()
        // viewAny: semua role terautentikasi
        ->and($siswa->can('viewAny', MaterialAttendance::class))->toBeTrue()
        ->and($guru->can('viewAny', MaterialAttendance::class))->toBeTrue()
        ->and($admin->can('viewAny', MaterialAttendance::class))->toBeTrue();
});

it('route mahasiswa dapat diakses oleh role mahasiswa (equivalence siswa)', function () {
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    $dosen = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    $course = Course::factory()->create(['instructor_id' => $dosen->id]);
    Enrollment::factory()->create([
        'user_id' => $mahasiswa->id,
        'course_id' => $course->id,
        'status' => 'active',
    ]);

    $this->actingAs($mahasiswa)->get(route('mahasiswa.attendance.index'))
        ->assertOk()
        ->assertViewIs('siswa.attendance.index');

    // Route siswa tidak bisa diakses mahasiswa langsung (role:siswa mengizinkan mahasiswa via equivalence)
    $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    $this->actingAs($siswa)->get(route('siswa.attendance.index'))->assertOk();
});

it('index menolak 404 jika materi bukan milik course', function () {
    ['course' => $course, 'material' => $material, 'guru' => $guru] = attendanceScenario();
    $courseLain = Course::factory()->create(['instructor_id' => $guru->id]);

    $this->actingAs($guru)
        ->get(route('guru.attendance.index', [$courseLain, $material]))
        ->assertNotFound();

    $this->actingAs($guru)->post(route('guru.attendance.save', [$courseLain, $material]), [
        'date' => '2026-10-04',
        'records' => [
            ['user_id' => 1, 'status' => 'hadir', 'alasan' => null],
        ],
    ])->assertNotFound();
});

it('model upsertBulk menghitung created dan updated dengan benar', function () {
    ['material' => $material, 'siswa' => $siswa, 'guru' => $guru] = attendanceScenario();

    $stats = MaterialAttendance::upsertBulk($material->id, '2026-10-04', [
        ['user_id' => $siswa->id, 'status' => 'hadir', 'alasan' => null],
    ], $guru->id);

    expect($stats)->toBe(['created' => 1, 'updated' => 0]);

    $stats = MaterialAttendance::upsertBulk($material->id, '2026-10-04', [
        ['user_id' => $siswa->id, 'status' => 'sakit', 'alasan' => 'Sakit kepala'],
    ], $guru->id);

    expect($stats)->toBe(['created' => 0, 'updated' => 1]);

    $attendance = MaterialAttendance::first();
    expect($attendance->status)->toBe('sakit')
        ->and($attendance->alasan)->toBe('Sakit kepala')
        ->and($attendance->status_display)->toBe('Sakit')
        ->and($attendance->recorded_by)->toBe($guru->id)
        ->and($attendance->student->id)->toBe($siswa->id)
        ->and($attendance->recorder->id)->toBe($guru->id)
        ->and($attendance->material->id)->toBe($material->id);
});
