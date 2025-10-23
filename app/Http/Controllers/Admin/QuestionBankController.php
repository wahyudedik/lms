<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionBankCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'question_image' => 'nullable|image|max:2048',
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
            'question_image' => 'nullable|image|max:2048',
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
}
