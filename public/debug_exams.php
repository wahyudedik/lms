<?php

// Temporary debug script for active exams
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Exam;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Exam Debugger</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-4">LMS Exam Debugger</h1>

        <!-- Part 1: Student Check -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-700 mb-3">1. Informasi Siswa</h2>
            <?php
            $studentName = "AHMAD ALIFIN FATIHUL IHSAN";
            $student = User::where('name', 'like', "%$studentName%")->first();

            if (!$student) {
                echo "<div class='bg-red-50 text-red-700 p-4 rounded-xl'>Siswa dengan nama '$studentName' tidak ditemukan di database.</div>";
            } else {
                echo "<div class='bg-blue-50 p-4 rounded-xl space-y-2'>";
                echo "<p><strong>Nama:</strong> {$student->name}</p>";
                echo "<p><strong>ID Siswa:</strong> {$student->id}</p>";
                echo "<p><strong>Role:</strong> {$student->role}</p>";
                echo "<p><strong>Status Aktif:</strong> " . ($student->is_active ? 'Ya (Aktif)' : 'Tidak') . "</p>";
                echo "</div>";
            }
            ?>
        </section>

        <!-- Part 2: Active Enrollments -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-700 mb-3">2. Pendaftaran Kursus Siswa (Enrollments)</h2>
            <?php
            if ($student) {
                $enrollments = Enrollment::where('user_id', $student->id)->with('course')->get();
                if ($enrollments->isEmpty()) {
                    echo "<div class='bg-yellow-50 text-yellow-700 p-4 rounded-xl'>Siswa tidak terdaftar di kursus apa pun.</div>";
                } else {
                    echo "<div class='overflow-x-auto'><table class='min-w-full bg-white border border-gray-200 rounded-lg'>";
                    echo "<thead class='bg-gray-50'><tr>";
                    echo "<th class='px-4 py-2 border-b text-left text-xs font-semibold text-gray-700'>ID Kursus</th>";
                    echo "<th class='px-4 py-2 border-b text-left text-xs font-semibold text-gray-700'>Nama Kursus</th>";
                    echo "<th class='px-4 py-2 border-b text-left text-xs font-semibold text-gray-700'>Status Pendaftaran</th>";
                    echo "</tr></thead><tbody>";
                    foreach ($enrollments as $e) {
                        $statusClass = $e->status === 'active' ? 'text-green-600 font-semibold' : 'text-gray-500';
                        echo "<tr>";
                        echo "<td class='px-4 py-2 border-b text-sm'>{$e->course_id}</td>";
                        echo "<td class='px-4 py-2 border-b text-sm'>{$e->course->title}</td>";
                        echo "<td class='px-4 py-2 border-b text-sm {$statusClass}'>{$e->status}</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table></div>";
                }
            }
            ?>
        </section>

        <!-- Part 3: Timezone check -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-700 mb-3">3. Konfigurasi Waktu Server & Database</h2>
            <div class="bg-gray-50 p-4 rounded-xl space-y-2 text-sm">
                <p><strong>PHP Timezone Config:</strong> <?php echo config('app.timezone'); ?></p>
                <p><strong>PHP Current Time (Local):</strong> <?php echo now()->toDateTimeString(); ?></p>
                <p><strong>PHP Current Time (UTC):</strong> <?php echo now()->setTimezone('UTC')->toDateTimeString(); ?></p>
                <p><strong>MySQL time_zone setting:</strong> <?php 
                    $tz = DB::select("SELECT @@session.time_zone as tz"); 
                    echo $tz[0]->tz; 
                ?></p>
                <p><strong>MySQL CURRENT_TIMESTAMP:</strong> <?php 
                    $dbTime = DB::select("SELECT CURRENT_TIMESTAMP() as t"); 
                    echo $dbTime[0]->t; 
                ?></p>
            </div>
        </section>

        <!-- Part 4: Exam Database Records & Condition Evaluations -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-blue-700 mb-3">4. Daftar Ujian & Evaluasi Syarat Tampil</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">ID</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Judul Ujian</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">ID Kursus</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Published</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Waktu Mulai (DB Raw)</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Waktu Selesai (DB Raw)</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Mulai <= Skrg?</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Selesai >= Skrg?</th>
                            <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-700">Aktif Scope?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $now = now()->setTimezone('UTC');
                        $nowStr = $now->toDateTimeString();

                        $exams = DB::table('exams')->get();
                        foreach ($exams as $exam) {
                            $isPublished = $exam->is_published ? 'Ya' : 'Tidak';
                            $startMatch = ($exam->start_time === null || $exam->start_time <= $nowStr) ? 'Ya' : 'Tidak';
                            $endMatch = ($exam->end_time === null || $exam->end_time >= $nowStr) ? 'Ya' : 'Tidak';
                            
                            // Check if it matches scopeActive
                            $isActiveScope = ($exam->is_published && 
                                             ($exam->start_time === null || $exam->start_time <= $nowStr) && 
                                             ($exam->end_time === null || $exam->end_time >= $nowStr)) ? 'Ya' : 'Tidak';

                            echo "<tr class='hover:bg-gray-50'>";
                            echo "<td class='px-4 py-2 border-b text-sm'>{$exam->id}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm font-semibold'>{$exam->title}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>{$exam->course_id}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>{$isPublished}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>{$exam->start_time}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>{$exam->end_time}</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>" . ($startMatch === 'Ya' ? "<span class='text-green-600 font-semibold'>Ya</span>" : "<span class='text-red-600'>Tidak</span>") . "</td>";
                            echo "<td class='px-4 py-2 border-b text-sm'>" . ($endMatch === 'Ya' ? "<span class='text-green-600 font-semibold'>Ya</span>" : "<span class='text-red-600'>Tidak</span>") . "</td>";
                            echo "<td class='px-4 py-2 border-b text-sm font-bold " . ($isActiveScope === 'Ya' ? 'text-green-600' : 'text-red-600') . "'>{$isActiveScope}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Part 5: Final query result -->
        <section class="mb-4">
            <h2 class="text-xl font-semibold text-blue-700 mb-3">5. Hasil Query Penggabungan (Daftar Ujian Terbaca)</h2>
            <?php
            if ($student) {
                $enrolledCourseIds = Enrollment::where('user_id', $student->id)
                    ->where('status', 'active')
                    ->pluck('course_id');

                $activeExams = Exam::whereIn('course_id', $enrolledCourseIds)
                    ->published()
                    ->active()
                    ->get();

                if ($activeExams->isEmpty()) {
                    echo "<div class='bg-red-50 text-red-700 p-4 rounded-xl'>Hasil query: <strong>0 Ujian Ditemukan</strong>. Ujian tidak muncul untuk siswa ini.</div>";
                } else {
                    echo "<div class='bg-green-50 text-green-700 p-4 rounded-xl mb-4'>Hasil query: <strong>" . $activeExams->count() . " Ujian Ditemukan</strong>.</div>";
                    echo "<ul class='list-disc pl-5 space-y-1'>";
                    foreach ($activeExams as $ae) {
                        echo "<li>ID: {$ae->id} | {$ae->title} (Mata Pelajaran: {$ae->course->title})</li>";
                    }
                    echo "</ul>";
                }
            }
            ?>
        </section>
    </div>
</body>
</html>
