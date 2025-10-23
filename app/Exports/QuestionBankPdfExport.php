<?php

namespace App\Exports;

use App\Models\QuestionBank;
use Barryvdh\DomPDF\Facade\Pdf;

class QuestionBankPdfExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Generate PDF export
     */
    public function download($filename = 'question-bank.pdf')
    {
        $query = QuestionBank::with(['category', 'creator']);

        // Apply filters
        if (isset($this->filters['category_id']) && $this->filters['category_id']) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (isset($this->filters['type']) && $this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if (isset($this->filters['difficulty']) && $this->filters['difficulty']) {
            $query->where('difficulty', $this->filters['difficulty']);
        }

        if (isset($this->filters['is_verified']) && $this->filters['is_verified'] !== '') {
            $query->where('is_verified', $this->filters['is_verified']);
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', $this->filters['is_active']);
        }

        $questions = $query->latest()->get();

        $data = [
            'questions' => $questions,
            'filters' => $this->filters,
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'total' => $questions->count(),
        ];

        $pdf = Pdf::loadView('admin.question-bank.pdf-export', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
