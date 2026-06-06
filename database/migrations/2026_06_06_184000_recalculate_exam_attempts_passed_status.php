<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite (testing), chunk and update in loop
            DB::table('exam_attempts')
                ->whereNotNull('score')
                ->orderBy('id')
                ->chunk(100, function ($attempts) {
                    foreach ($attempts as $attempt) {
                        $exam = DB::table('exams')->where('id', $attempt->exam_id)->first();
                        if ($exam) {
                            $passed = $attempt->score >= $exam->pass_score ? 1 : 0;
                            DB::table('exam_attempts')
                                ->where('id', $attempt->id)
                                ->update(['passed' => $passed]);
                        }
                    }
                });
        } else {
            // For MySQL (production), use performant join update
            DB::table('exam_attempts')
                ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
                ->whereNotNull('exam_attempts.score')
                ->update([
                    'exam_attempts.passed' => DB::raw('CASE WHEN exam_attempts.score >= exams.pass_score THEN 1 ELSE 0 END')
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
