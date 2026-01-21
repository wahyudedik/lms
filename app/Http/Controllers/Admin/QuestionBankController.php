<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\QuestionBankTemplateExport;
use App\Models\QuestionBank;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankImportHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\QuestionBankExport;
use App\Exports\QuestionBankPdfExport;
use App\Exports\QuestionBankJsonExport;
use App\Imports\QuestionBankImport;
use App\Jobs\ProcessQuestionBankImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the question bank
     */
    public function index(Request $request)
    {
        $query = QuestionBank::with(['category', 'creator']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by verification
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified === 'yes');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $questions = $query->paginate(20)->withQueryString();
        $categories = QuestionBankCategory::active()->orderBy('name')->get();

        return view('admin.question-bank.index', compact('questions', 'categories'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        $categories = QuestionBankCategory::active()->with('parent')->orderBy('name')->get();
        return view('admin.question-bank.create', compact('categories'));
    }

    /**
     * Store a newly created question in storage
     */
    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'nullable|exists:question_bank_categories,id',
            'type' => 'required|in:mcq_single,mcq_multiple,matching,essay',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
            'default_points' => 'required|numeric|min:0.01',
            'explanation' => 'nullable|string',
            'teacher_notes' => 'nullable|string',
        ];

        // Type-specific validation
        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';

            if ($request->type === 'mcq_single') {
                $rules['correct_answer'] = 'required|string';
            } else {
                $rules['correct_answer_multiple'] = 'required|array|min:1';
                $rules['correct_answer_multiple.*'] = 'required|string';
            }
        }

        if ($request->type === 'matching') {
            $rules['pairs'] = 'required|array|min:2';
            $rules['pairs.*.left'] = 'required|string';
            $rules['pairs.*.right'] = 'required|string';
        }

        if ($request->type === 'essay') {
            $rules['essay_grading_mode'] = 'required|in:manual,keyword,similarity';

            if ($request->essay_grading_mode === 'keyword') {
                $rules['essay_keywords'] = 'required|array|min:1';
                $rules['essay_keywords.*.keyword'] = 'required|string';
                $rules['essay_keywords.*.points'] = 'required|numeric|min:0';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|numeric|min:0|max:100';
            }
        }

        $validated = $request->validate($rules);

        // Handle image upload
        if ($request->hasFile('question_image')) {
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        // Parse tags
        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $question = QuestionBank::create($validated);

        return redirect()
            ->route('admin.question-bank.show', $question)
            ->with('success', 'Question added to bank successfully!');
    }

    /**
     * Display the specified question
     */
    public function show(QuestionBank $questionBank)
    {
        $questionBank->load(['category', 'creator', 'examQuestions.exam']);
        return view('admin.question-bank.show', compact('questionBank'));
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(QuestionBank $questionBank)
    {
        $categories = QuestionBankCategory::active()->with('parent')->orderBy('name')->get();
        return view('admin.question-bank.edit', compact('questionBank', 'categories'));
    }

    /**
     * Update the specified question in storage
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        // Same validation as store
        $rules = [
            'category_id' => 'nullable|exists:question_bank_categories,id',
            'type' => 'required|in:mcq_single,mcq_multiple,matching,essay',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
            'default_points' => 'required|numeric|min:0.01',
            'explanation' => 'nullable|string',
            'teacher_notes' => 'nullable|string',
        ];

        // Type-specific validation (same as store)
        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*'] = 'required|string';

            if ($request->type === 'mcq_single') {
                $rules['correct_answer'] = 'required|string';
            } else {
                $rules['correct_answer_multiple'] = 'required|array|min:1';
                $rules['correct_answer_multiple.*'] = 'required|string';
            }
        }

        if ($request->type === 'matching') {
            $rules['pairs'] = 'required|array|min:2';
            $rules['pairs.*.left'] = 'required|string';
            $rules['pairs.*.right'] = 'required|string';
        }

        if ($request->type === 'essay') {
            $rules['essay_grading_mode'] = 'required|in:manual,keyword,similarity';

            if ($request->essay_grading_mode === 'keyword') {
                $rules['essay_keywords'] = 'required|array|min:1';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|numeric|min:0|max:100';
            }
        }

        $validated = $request->validate($rules);

        // Handle image upload
        if ($request->hasFile('question_image')) {
            // Delete old image
            if ($questionBank->question_image) {
                Storage::disk('public')->delete($questionBank->question_image);
            }
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        // Parse tags
        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        $validated['is_active'] = $request->has('is_active');

        $questionBank->update($validated);

        return redirect()
            ->route('admin.question-bank.show', $questionBank)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified question from storage
     */
    public function destroy(QuestionBank $questionBank)
    {
        // Delete image if exists
        if ($questionBank->question_image) {
            Storage::disk('public')->delete($questionBank->question_image);
        }

        $questionBank->delete();

        return redirect()
            ->route('admin.question-bank.index')
            ->with('success', 'Question deleted from bank!');
    }

    /**
     * Toggle verification status
     */
    public function toggleVerification(QuestionBank $questionBank)
    {
        $questionBank->is_verified = !$questionBank->is_verified;
        $questionBank->save();

        $status = $questionBank->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Question marked as {$status}!");
    }

    /**
     * Duplicate question
     */
    public function duplicate(QuestionBank $questionBank)
    {
        $newQuestion = $questionBank->replicate();
        $newQuestion->question_text = $questionBank->question_text . ' (Copy)';
        $newQuestion->times_used = 0;
        $newQuestion->average_score = null;
        $newQuestion->times_correct = 0;
        $newQuestion->times_incorrect = 0;
        $newQuestion->is_verified = false;
        $newQuestion->save();

        return redirect()
            ->route('admin.question-bank.edit', $newQuestion)
            ->with('success', 'Question duplicated! You can now edit it.');
    }

    /**
     * Show statistics dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => QuestionBank::count(),
            'active' => QuestionBank::where('is_active', true)->count(),
            'verified' => QuestionBank::where('is_verified', true)->count(),
            'by_type' => QuestionBank::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_difficulty' => QuestionBank::selectRaw('difficulty, COUNT(*) as count')
                ->groupBy('difficulty')
                ->pluck('count', 'difficulty'),
            'by_category' => QuestionBankCategory::withCount('questions')
                ->having('questions_count', '>', 0)
                ->get(),
            'most_used' => QuestionBank::where('times_used', '>', 0)
                ->orderBy('times_used', 'desc')
                ->limit(10)
                ->get(),
            'best_performing' => QuestionBank::whereNotNull('average_score')
                ->where('times_used', '>=', 5)
                ->orderBy('average_score', 'desc')
                ->limit(10)
                ->get(),
            'worst_performing' => QuestionBank::whereNotNull('average_score')
                ->where('times_used', '>=', 5)
                ->orderBy('average_score', 'asc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.question-bank.statistics', compact('stats'));
    }

    /**
     * Get questions for import modal (AJAX)
     */
    public function getForImport()
    {
        $questions = QuestionBank::with('category')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'type' => $q->type,
                    'difficulty' => $q->difficulty,
                    'default_points' => $q->default_points,
                    'category_id' => $q->category_id,
                    'category_name' => $q->category ? $q->category->name : null,
                    'times_used' => $q->times_used,
                ];
            });

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }

    /**
     * Get random questions for AJAX
     */
    public function getRandom(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:question_bank_categories,id',
            'easy' => 'required|integer|min:0',
            'medium' => 'required|integer|min:0',
            'hard' => 'required|integer|min:0',
        ]);

        $questions = collect();

        $baseQuery = QuestionBank::active();
        if ($request->filled('category_id')) {
            $baseQuery = $baseQuery->where('category_id', $request->category_id);
        }

        if ($request->easy > 0) {
            $easy = (clone $baseQuery)->where('difficulty', 'easy')
                ->inRandomOrder()
                ->limit($request->easy)
                ->get();
            $questions = $questions->concat($easy);
        }

        if ($request->medium > 0) {
            $medium = (clone $baseQuery)->where('difficulty', 'medium')
                ->inRandomOrder()
                ->limit($request->medium)
                ->get();
            $questions = $questions->concat($medium);
        }

        if ($request->hard > 0) {
            $hard = (clone $baseQuery)->where('difficulty', 'hard')
                ->inRandomOrder()
                ->limit($request->hard)
                ->get();
            $questions = $questions->concat($hard);
        }

        return response()->json([
            'success' => true,
            'questions' => $questions,
            'count' => $questions->count(),
        ]);
    }

    /**
     * Export question bank (supports multiple formats)
     */
    public function export(Request $request)
    {
        $filters = $request->only(['category_id', 'type', 'difficulty', 'is_verified', 'is_active']);
        $format = $request->get('format', 'excel'); // excel, pdf, json

        $timestamp = date('Y-m-d-His');
        $filename = 'question-bank-' . $timestamp;

        switch ($format) {
            case 'pdf':
                return (new QuestionBankPdfExport($filters))->download($filename . '.pdf');

            case 'json':
                $data = (new QuestionBankJsonExport($filters))->export();
                return response()->json($data)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');

            case 'excel':
            default:
                return Excel::download(new QuestionBankExport($filters), $filename . '.xlsx');
        }
    }

    /**
     * Import questions from Excel (with optional queuing)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'use_queue' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $useQueue = $request->boolean('use_queue');

        // Create import history record
        $importHistory = QuestionBankImportHistory::create([
            'user_id' => Auth::id(),
            'filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        if ($useQueue) {
            // Store file temporarily
            $path = $file->store('temp-imports');
            $importHistory->update(['file_path' => $path]);

            // Dispatch to queue
            ProcessQuestionBankImport::dispatch($importHistory, $path);

            return redirect()->back()->with('success', 'Import queued successfully! Check import history for progress.');
        } else {
            // Process immediately
            try {
                $importHistory->markAsProcessing();

                $import = new QuestionBankImport();
                Excel::import($import, $file);

                $stats = $import->getStats();
                $importHistory->markAsCompleted($stats);

                if ($stats['imported'] > 0) {
                    $message = "{$stats['imported']} questions imported successfully.";

                    if ($stats['skipped'] > 0) {
                        $message .= " {$stats['skipped']} questions were skipped.";
                    }

                    return redirect()->back()->with('success', $message);
                } else {
                    return redirect()->back()->with('error', 'No questions were imported. ' . implode(' ', $stats['errors']));
                }
            } catch (\Exception $e) {
                $importHistory->markAsFailed($e->getMessage());
                return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'excel' || $format === 'xlsx') {
            return Excel::download(new QuestionBankTemplateExport(), 'question-bank-import-template.xlsx');
        }

        $headers = [
            ['type', 'difficulty', 'question_text', 'category', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer', 'correct_answers', 'default_points', 'explanation', 'tags', 'image_url', 'is_active', 'is_verified'],
            ['mcq_single', 'easy', 'What is 2+2?', 'Mathematics', '1', '2', '3', '4', '', '4', '', '1', 'Simple addition', 'math, basic', '', 'yes', 'yes'],
            ['mcq_multiple', 'medium', 'Select prime numbers', 'Mathematics', '1', '2', '3', '4', '5', '', '2,3,5', '2', 'Prime numbers are divisible by 1 and themselves', 'math, prime', '', 'yes', 'no'],
            ['essay', 'hard', 'Explain photosynthesis', 'Biology', '', '', '', '', '', '', '', '5', 'Process by which plants make food', 'biology, science', '', 'yes', 'yes'],
        ];

        $filename = 'question-bank-import-template.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($headers as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Validate import file before actual import
     */
    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new QuestionBankImport();
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();

            return response()->json([
                'success' => true,
                'validation' => [
                    'total_rows' => $stats['imported'] + $stats['skipped'],
                    'valid_rows' => $stats['imported'],
                    'invalid_rows' => $stats['skipped'],
                    'errors' => $stats['errors'],
                ],
                'message' => 'Validation completed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * View import history
     */
    public function importHistory(Request $request)
    {
        $imports = QuestionBankImportHistory::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.question-bank.import-history', compact('imports'));
    }

    /**
     * Show import history details
     */
    public function importHistoryShow(QuestionBankImportHistory $importHistory)
    {
        $importHistory->load('user');

        return view('admin.question-bank.import-history-show', compact('importHistory'));
    }

    /**
     * Delete import history record
     */
    public function importHistoryDelete(QuestionBankImportHistory $importHistory)
    {
        // Delete associated file if exists
        if ($importHistory->file_path && Storage::exists($importHistory->file_path)) {
            Storage::delete($importHistory->file_path);
        }

        $importHistory->delete();

        return redirect()->back()->with('success', 'Import history deleted successfully.');
    }

    /**
     * Export by specific category
     */
    public function exportByCategory(Request $request, QuestionBankCategory $category)
    {
        $format = $request->get('format', 'excel');
        $filters = ['category_id' => $category->id];

        $timestamp = date('Y-m-d-His');
        $categorySlug = \Str::slug($category->name);
        $filename = "question-bank-{$categorySlug}-{$timestamp}";

        switch ($format) {
            case 'pdf':
                return (new QuestionBankPdfExport($filters))->download($filename . '.pdf');

            case 'json':
                $data = (new QuestionBankJsonExport($filters))->export();
                return response()->json($data)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');

            case 'excel':
            default:
                return Excel::download(new QuestionBankExport($filters), $filename . '.xlsx');
        }
    }
}
