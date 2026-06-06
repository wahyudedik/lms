<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$exams = DB::table('exams')->select('id', 'title', 'start_time', 'end_time')->get();
foreach ($exams as $exam) {
    echo "- Exam ID: " . $exam->id . " | Title: " . $exam->title . " | Raw Start: " . $exam->start_time . " | Raw End: " . $exam->end_time . "\n";
}
