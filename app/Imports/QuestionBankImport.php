<?php

namespace App\Imports;

use App\Models\QuestionBank;
use App\Models\QuestionBankCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionBankImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-index

            try {
                // Validate row
                $validator = Validator::make($row->toArray(), [
                    'type' => 'required|in:mcq_single,mcq_multiple,matching,essay',
                    'difficulty' => 'required|in:easy,medium,hard',
                    'question_text' => 'required|string',
                    'default_points' => 'nullable|numeric|min:0',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->skipped++;
                    continue;
                }

                // Find or create category
                $category = null;
                if (!empty($row['category'])) {
                    $category = QuestionBankCategory::firstOrCreate(
                        ['name' => $row['category']],
                        [
                            'slug' => \Str::slug($row['category']),
                            'description' => 'Auto-created from import',
                            'is_active' => true,
                        ]
                    );
                }

                // Parse JSON fields
                $options = null;
                if (!empty($row['options_json'])) {
                    $options = json_decode($row['options_json'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $options = null;
                    }
                }

                $correctAnswer = null;
                if (!empty($row['correct_answer_json'])) {
                    $correctAnswer = json_decode($row['correct_answer_json'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $correctAnswer = null;
                    }
                }

                // Parse tags
                $tags = null;
                if (!empty($row['tags'])) {
                    $tags = array_map('trim', explode(',', $row['tags']));
                }

                // Create question
                QuestionBank::create([
                    'category_id' => $category ? $category->id : null,
                    'type' => $row['type'],
                    'difficulty' => $row['difficulty'],
                    'question_text' => $row['question_text'],
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                    'default_points' => $row['default_points'] ?? 1,
                    'explanation' => $row['explanation'] ?? null,
                    'tags' => $tags,
                    'question_image' => $row['image_url'] ?? null,
                    'is_active' => isset($row['is_active']) ?
                        (strtolower($row['is_active']) === 'yes' || $row['is_active'] === '1' || $row['is_active'] === true) : true,
                    'is_verified' => isset($row['is_verified']) ?
                        (strtolower($row['is_verified']) === 'yes' || $row['is_verified'] === '1' || $row['is_verified'] === true) : false,
                    'created_by' => Auth::id(),
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    /**
     * Get import statistics
     */
    public function getStats()
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }
}
