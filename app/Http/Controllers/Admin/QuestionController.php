<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for an exam
     */
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->orderBy('order')->get();
        $categories = \App\Models\QuestionBankCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.questions.index', compact('exam', 'questions', 'categories'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    /**
     * Store a newly created question in storage
     */
    public function store(Request $request, Exam $exam)
    {
        // Base validation
        $rules = [
            'type' => 'required|in:mcq_single,mcq_multiple,matching,essay',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'points' => 'required|numeric|min:0',
            'order' => 'nullable|integer',
            'explanation' => 'nullable|string',
        ];

        // Add type-specific validation
        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.id'] = 'required|string';
            $rules['options.*.text'] = 'required|string';
        }

        if ($request->type === 'mcq_single') {
            $rules['correct_answer_single'] = 'required|string';
        }

        if ($request->type === 'mcq_multiple') {
            $rules['correct_answer_multiple'] = 'required|array|min:1';
            $rules['correct_answer_multiple.*'] = 'required|string';
        }

        if ($request->type === 'matching') {
            $rules['pairs'] = 'required|array|min:2';
            $rules['pairs.*.left'] = 'required|string';
            $rules['pairs.*.right'] = 'required|string';
        }

        if ($request->type === 'essay') {
            $rules['essay_grading_mode'] = 'required|in:manual,keyword,similarity';
            $rules['essay_case_sensitive'] = 'nullable|boolean';

            if ($request->essay_grading_mode === 'keyword') {
                $rules['essay_keywords'] = 'required|array|min:1';
                $rules['essay_keywords.*'] = 'required|string';
                $rules['essay_keyword_points'] = 'required|array|min:1';
                $rules['essay_keyword_points.*'] = 'required|numeric|min:0';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|integer|min:0|max:100';
            }
        }

        $validated = $request->validate($rules, [
            'pairs.*.left.required' => 'Item kiri pada pasangan harus diisi.',
            'pairs.*.right.required' => 'Item kanan pada pasangan harus diisi.',
            'pairs.min' => 'Minimal harus ada 2 pasangan untuk soal menjodohkan.',
            'options.*.text.required' => 'Teks opsi harus diisi.',
            'options.min' => 'Minimal harus ada 2 opsi.',
            'correct_answer_single.required' => 'Pilih jawaban benar untuk pilihan ganda.',
            'correct_answer_multiple.required' => 'Pilih minimal satu jawaban benar.',
            'correct_answer_multiple.min' => 'Pilih minimal satu jawaban benar.',
            'essay_keywords.required' => 'Minimal harus ada 1 kata kunci untuk mode keyword matching.',
            'essay_model_answer.required' => 'Jawaban model harus diisi untuk mode similarity matching.',
        ]);

        $questionData = [
            'exam_id' => $exam->id,
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? ($exam->questions()->max('order') + 1),
            'explanation' => $validated['explanation'] ?? null,
        ];

        // Handle question image upload
        if ($request->hasFile('question_image')) {
            $path = $request->file('question_image')->store('question-images', 'public');
            $questionData['question_image'] = $path;
        }

        // Handle different question types
        if ($validated['type'] === 'mcq_single' || $validated['type'] === 'mcq_multiple') {
            $questionData['options'] = $validated['options'];

            if ($validated['type'] === 'mcq_single') {
                $questionData['correct_answer'] = $validated['correct_answer_single'];
            } else {
                $questionData['correct_answer'] = $validated['correct_answer_multiple'];
            }
        } elseif ($validated['type'] === 'matching') {
            $questionData['pairs'] = $validated['pairs'];
            $questionData['correct_answer'] = $validated['pairs'];
        } elseif ($validated['type'] === 'essay') {
            // Handle essay grading configuration
            $questionData['essay_grading_mode'] = $validated['essay_grading_mode'];
            $questionData['essay_case_sensitive'] = $request->has('essay_case_sensitive') ? true : false;

            if ($validated['essay_grading_mode'] === 'keyword') {
                $questionData['essay_keywords'] = $validated['essay_keywords'];
                $questionData['essay_keyword_points'] = $validated['essay_keyword_points'];
            } elseif ($validated['essay_grading_mode'] === 'similarity') {
                $questionData['essay_model_answer'] = $validated['essay_model_answer'];
                $questionData['essay_min_similarity'] = $validated['essay_min_similarity'];
            }
        }

        Question::create($questionData);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(Exam $exam, Question $question)
    {
        return view('admin.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified question in storage
     */
    public function update(Request $request, Exam $exam, Question $question)
    {
        // Base validation
        $rules = [
            'type' => 'required|in:mcq_single,mcq_multiple,matching,essay',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'points' => 'required|numeric|min:0',
            'order' => 'nullable|integer',
            'explanation' => 'nullable|string',
        ];

        // Add type-specific validation
        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.id'] = 'required|string';
            $rules['options.*.text'] = 'required|string';
        }

        if ($request->type === 'mcq_single') {
            $rules['correct_answer_single'] = 'required|string';
        }

        if ($request->type === 'mcq_multiple') {
            $rules['correct_answer_multiple'] = 'required|array|min:1';
            $rules['correct_answer_multiple.*'] = 'required|string';
        }

        if ($request->type === 'matching') {
            $rules['pairs'] = 'required|array|min:2';
            $rules['pairs.*.left'] = 'required|string';
            $rules['pairs.*.right'] = 'required|string';
        }

        if ($request->type === 'essay') {
            $rules['essay_grading_mode'] = 'required|in:manual,keyword,similarity';
            $rules['essay_case_sensitive'] = 'nullable|boolean';

            if ($request->essay_grading_mode === 'keyword') {
                $rules['essay_keywords'] = 'required|array|min:1';
                $rules['essay_keywords.*'] = 'required|string';
                $rules['essay_keyword_points'] = 'required|array|min:1';
                $rules['essay_keyword_points.*'] = 'required|numeric|min:0';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|integer|min:0|max:100';
            }
        }

        $validated = $request->validate($rules, [
            'pairs.*.left.required' => 'Item kiri pada pasangan harus diisi.',
            'pairs.*.right.required' => 'Item kanan pada pasangan harus diisi.',
            'pairs.min' => 'Minimal harus ada 2 pasangan untuk soal menjodohkan.',
            'options.*.text.required' => 'Teks opsi harus diisi.',
            'options.min' => 'Minimal harus ada 2 opsi.',
            'correct_answer_single.required' => 'Pilih jawaban benar untuk pilihan ganda.',
            'correct_answer_multiple.required' => 'Pilih minimal satu jawaban benar.',
            'correct_answer_multiple.min' => 'Pilih minimal satu jawaban benar.',
            'essay_keywords.required' => 'Minimal harus ada 1 kata kunci untuk mode keyword matching.',
            'essay_model_answer.required' => 'Jawaban model harus diisi untuk mode similarity matching.',
        ]);

        $questionData = [
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'points' => $validated['points'],
            'order' => $validated['order'] ?? $question->order,
            'explanation' => $validated['explanation'] ?? null,
        ];

        // Handle question image upload
        if ($request->hasFile('question_image')) {
            // Delete old image if exists
            if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
                Storage::disk('public')->delete($question->question_image);
            }

            $path = $request->file('question_image')->store('question-images', 'public');
            $questionData['question_image'] = $path;
        }

        // Handle different question types
        if ($validated['type'] === 'mcq_single' || $validated['type'] === 'mcq_multiple') {
            $questionData['options'] = $validated['options'];

            if ($validated['type'] === 'mcq_single') {
                $questionData['correct_answer'] = $validated['correct_answer_single'];
            } else {
                $questionData['correct_answer'] = $validated['correct_answer_multiple'];
            }

            $questionData['pairs'] = null;
        } elseif ($validated['type'] === 'matching') {
            $questionData['pairs'] = $validated['pairs'];
            $questionData['correct_answer'] = $validated['pairs'];
            $questionData['options'] = null;
        } elseif ($validated['type'] === 'essay') {
            // Handle essay grading configuration
            $questionData['essay_grading_mode'] = $validated['essay_grading_mode'];
            $questionData['essay_case_sensitive'] = $request->has('essay_case_sensitive') ? true : false;

            if ($validated['essay_grading_mode'] === 'keyword') {
                $questionData['essay_keywords'] = $validated['essay_keywords'];
                $questionData['essay_keyword_points'] = $validated['essay_keyword_points'];
                // Clear similarity fields
                $questionData['essay_model_answer'] = null;
            } elseif ($validated['essay_grading_mode'] === 'similarity') {
                $questionData['essay_model_answer'] = $validated['essay_model_answer'];
                $questionData['essay_min_similarity'] = $validated['essay_min_similarity'];
                // Clear keyword fields
                $questionData['essay_keywords'] = null;
                $questionData['essay_keyword_points'] = null;
            } else {
                // Manual mode - clear all auto-grading fields
                $questionData['essay_keywords'] = null;
                $questionData['essay_keyword_points'] = null;
                $questionData['essay_model_answer'] = null;
            }

            $questionData['options'] = null;
            $questionData['pairs'] = null;
            $questionData['correct_answer'] = null;
        }

        $question->update($questionData);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Remove the specified question from storage
     */
    public function destroy(Exam $exam, Question $question)
    {
        // Delete question image if exists
        if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
            Storage::disk('public')->delete($question->question_image);
        }

        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Reorder questions
     */
    public function reorder(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        foreach ($validated['question_ids'] as $index => $questionId) {
            Question::where('id', $questionId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan soal berhasil diperbarui!']);
    }

    /**
     * Duplicate a question
     */
    public function duplicate(Exam $exam, Question $question)
    {
        $newQuestion = $question->replicate();
        $newQuestion->order = $exam->questions()->max('order') + 1;
        $newQuestion->save();

        return back()->with('success', 'Soal berhasil diduplikasi!');
    }

    /**
     * Import questions from bank
     */
    public function importFromBank(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:question_bank,id',
        ]);

        $nextOrder = $exam->questions()->max('order') + 1;
        $imported = 0;

        foreach ($validated['question_ids'] as $bankQuestionId) {
            $bankQuestion = \App\Models\QuestionBank::find($bankQuestionId);

            if ($bankQuestion) {
                $bankQuestion->cloneToExam($exam->id, $nextOrder);
                $nextOrder++;
                $imported++;
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
            'message' => "$imported soal berhasil diimport dari Question Bank!",
        ]);
    }
}
