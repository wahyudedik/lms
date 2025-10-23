<?php

namespace App\Exports;

use App\Models\QuestionBank;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionBankExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
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

        return $query->latest()->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Category',
            'Type',
            'Difficulty',
            'Question Text',
            'Options (JSON)',
            'Correct Answer (JSON)',
            'Points',
            'Explanation',
            'Tags',
            'Image URL',
            'Is Active',
            'Is Verified',
            'Times Used',
            'Created By',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param mixed $question
     * @return array
     */
    public function map($question): array
    {
        return [
            $question->id,
            $question->category ? $question->category->name : '',
            $question->type,
            $question->difficulty,
            $question->question_text,
            json_encode($question->options),
            json_encode($question->correct_answer),
            $question->default_points,
            $question->explanation,
            $question->tags ? implode(', ', $question->tags) : '',
            $question->question_image,
            $question->is_active ? 'Yes' : 'No',
            $question->is_verified ? 'Yes' : 'No',
            $question->times_used,
            $question->creator ? $question->creator->name : '',
            $question->created_at->format('Y-m-d H:i:s'),
            $question->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold header
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
