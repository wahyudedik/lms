<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Models\QuestionBank;
use App\Models\QuestionBankCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    use ResolvesRolePrefix;

    /**
     * Display a listing of the teacher's question bank.
     */
    public function index(Request $request)
    {
        $query = QuestionBank::with(['category'])
            ->where('created_by', auth()->id());

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $categories = QuestionBankCategory::active()->orderBy('name')->get();

        return view('guru.question-bank.index', compact('questions', 'categories'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        $categories = QuestionBankCategory::active()->with('parent')->orderBy('name')->get();

        return view('guru.question-bank.create', compact('categories'));
    }

    /**
     * Store a newly created question in storage.
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

        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'required|string';

            if ($request->type === 'mcq_single') {
                $rules['correct_answer_single'] = 'required|string';
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
                $rules['essay_keywords.*'] = 'required|string';
                $rules['essay_keyword_points'] = 'required|array|min:1';
                $rules['essay_keyword_points.*'] = 'required|numeric|min:0';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|numeric|min:0|max:100';
            }
        }

        $validated = $request->validate($rules, [
            'correct_answer_single.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer_multiple.required' => 'Pilih minimal satu jawaban benar.',
            'options.required' => 'Opsi jawaban wajib diisi.',
            'options.*.text.required' => 'Teks setiap opsi wajib diisi.',
            'pairs.required' => 'Pasangan jawaban wajib diisi.',
            'pairs.*.left.required' => 'Kolom kiri pasangan wajib diisi.',
            'pairs.*.right.required' => 'Kolom kanan pasangan wajib diisi.',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        if (in_array($request->type, ['mcq_single', 'mcq_multiple']) && $request->has('options')) {
            $validated['options'] = collect($request->options)
                ->filter(fn ($opt) => !empty($opt['text']))
                ->values()
                ->map(fn ($opt) => ['id' => $opt['id'] ?? '', 'text' => $opt['text']])
                ->toArray();
        }

        if ($request->type === 'mcq_single') {
            $validated['correct_answer'] = $validated['correct_answer_single'] ?? '';
            unset($validated['correct_answer_single']);
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_shared'] = $request->has('is_shared');

        $question = QuestionBank::create($validated);

        return redirect()
            ->to($this->teacherRoute('question-bank.show', $question))
            ->with('success', 'Soal berhasil ditambahkan ke bank soal!');
    }

    /**
     * Display the specified question.
     */
    public function show(QuestionBank $questionBank)
    {
        $this->authorizeOwnership($questionBank);

        $questionBank->load(['category', 'examQuestions.exam']);

        return view('guru.question-bank.show', compact('questionBank'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(QuestionBank $questionBank)
    {
        $this->authorizeOwnership($questionBank);

        $categories = QuestionBankCategory::active()->with('parent')->orderBy('name')->get();

        return view('guru.question-bank.edit', compact('questionBank', 'categories'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $this->authorizeOwnership($questionBank);

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

        if ($request->type === 'mcq_single' || $request->type === 'mcq_multiple') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'required|string';

            if ($request->type === 'mcq_single') {
                $rules['correct_answer_single'] = 'required|string';
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
                $rules['essay_keywords.*'] = 'required|string';
                $rules['essay_keyword_points'] = 'required|array|min:1';
                $rules['essay_keyword_points.*'] = 'required|numeric|min:0';
            }

            if ($request->essay_grading_mode === 'similarity') {
                $rules['essay_model_answer'] = 'required|string';
                $rules['essay_min_similarity'] = 'required|numeric|min:0|max:100';
            }
        }

        $validated = $request->validate($rules, [
            'correct_answer_single.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer_multiple.required' => 'Pilih minimal satu jawaban benar.',
            'options.required' => 'Opsi jawaban wajib diisi.',
            'options.*.text.required' => 'Teks setiap opsi wajib diisi.',
            'pairs.required' => 'Pasangan jawaban wajib diisi.',
            'pairs.*.left.required' => 'Kolom kiri pasangan wajib diisi.',
            'pairs.*.right.required' => 'Kolom kanan pasangan wajib diisi.',
        ]);

        if ($request->hasFile('question_image')) {
            if ($questionBank->question_image) {
                Storage::disk('public')->delete($questionBank->question_image);
            }
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        if (in_array($request->type, ['mcq_single', 'mcq_multiple']) && $request->has('options')) {
            $validated['options'] = collect($request->options)
                ->filter(fn ($opt) => !empty($opt['text']))
                ->values()
                ->map(fn ($opt) => ['id' => $opt['id'] ?? '', 'text' => $opt['text']])
                ->toArray();
        }

        if ($request->type === 'mcq_single') {
            $validated['correct_answer'] = $validated['correct_answer_single'] ?? '';
            unset($validated['correct_answer_single']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_shared'] = $request->has('is_shared');

        $questionBank->update($validated);

        return redirect()
            ->to($this->teacherRoute('question-bank.show', $questionBank))
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(QuestionBank $questionBank)
    {
        $this->authorizeOwnership($questionBank);

        if ($questionBank->question_image) {
            Storage::disk('public')->delete($questionBank->question_image);
        }

        $questionBank->delete();

        return redirect()
            ->to($this->teacherRoute('question-bank.index'))
            ->with('success', 'Soal berhasil dihapus dari bank soal!');
    }

    /**
     * Duplicate a question.
     */
    public function duplicate(QuestionBank $questionBank)
    {
        $this->authorizeOwnership($questionBank);

        $newQuestion = $questionBank->replicate();
        $newQuestion->question_text = $questionBank->question_text . ' (Salinan)';
        $newQuestion->times_used = 0;
        $newQuestion->average_score = null;
        $newQuestion->times_correct = 0;
        $newQuestion->times_incorrect = 0;
        $newQuestion->is_verified = false;
        $newQuestion->created_by = auth()->id();
        $newQuestion->save();

        return redirect()
            ->to($this->teacherRoute('question-bank.edit', $newQuestion))
            ->with('success', 'Soal berhasil diduplikasi!');
    }

    /**
     * Ensure the authenticated user owns this question.
     */
    private function authorizeOwnership(QuestionBank $questionBank): void
    {
        if ($questionBank->created_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
        }
    }
}
