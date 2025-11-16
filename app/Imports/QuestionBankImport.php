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
            $rowData = array_map(function ($v) {
                if (is_string($v)) {
                    return trim($v);
                }
                return $v;
            }, $row->toArray());

            // Skip completely empty rows (prevents noisy "required" errors for trailing lines)
            $nonEmptyKeys = array_filter($rowData, function ($v) {
                return !($v === null || $v === '');
            });
            if (empty($nonEmptyKeys)) {
                continue;
            }

            // Normalize default_points early (accept blanks, text, comma decimals)
            $rowData['default_points'] = $this->normalizePoints($rowData['default_points'] ?? null);

            try {
                // Validate row
                $validator = Validator::make($rowData, [
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
                if (!empty($rowData['category'])) {
                    $category = QuestionBankCategory::firstOrCreate(
                        ['name' => $rowData['category']],
                        [
                            'slug' => \Str::slug($rowData['category']),
                            'description' => 'Auto-created from import',
                            'is_active' => true,
                        ]
                    );
                }

                $options = $this->parseOptions($rowData);
                [$singleAnswer, $multipleAnswers] = $this->parseCorrectAnswers($rowData);
                $pairs = $this->parsePairs($rowData);
                $tags = $this->parseTags($rowData);

                // Create question
                QuestionBank::create([
                    'category_id' => $category ? $category->id : null,
                    'type' => $rowData['type'],
                    'difficulty' => $rowData['difficulty'],
                    'question_text' => $rowData['question_text'],
                    'options' => $options,
                    'correct_answer' => $rowData['type'] === 'mcq_single' ? $singleAnswer : null,
                    'correct_answer_multiple' => $rowData['type'] === 'mcq_multiple' ? $multipleAnswers : null,
                    'pairs' => $rowData['type'] === 'matching' ? $pairs : null,
                    'default_points' => ($rowData['default_points'] ?? null) !== null ? $rowData['default_points'] : 1,
                    'explanation' => $rowData['explanation'] ?? null,
                    'tags' => $tags,
                    'question_image' => $rowData['image_url'] ?? null,
                    'is_active' => isset($rowData['is_active']) ?
                        (strtolower($rowData['is_active']) === 'yes' || $rowData['is_active'] === '1' || $rowData['is_active'] === true) : true,
                    'is_verified' => isset($rowData['is_verified']) ?
                        (strtolower($rowData['is_verified']) === 'yes' || $rowData['is_verified'] === '1' || $rowData['is_verified'] === true) : false,
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

    /**
     * Parse options from JSON or column-based inputs
     */
    protected function parseOptions(array $row): ?array
    {
        if (!empty($row['options_json'])) {
            $decoded = json_decode($row['options_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_filter($decoded, fn($value) => $value !== null && $value !== ''));
            }
        }

        $options = [];
        $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'option_f'];
        foreach ($optionKeys as $key) {
            if (!empty($row[$key])) {
                $options[] = trim($row[$key]);
            }
        }

        if (empty($options) && !empty($row['options'])) {
            $options = array_filter(array_map('trim', preg_split('/[|;,]+/', $row['options'])));
        }

        return $options ? array_values($options) : null;
    }

    /**
     * Parse correct answers for single or multiple choice questions
     */
    protected function parseCorrectAnswers(array $row): array
    {
        $type = $row['type'] ?? null;

        $decoded = null;
        if (!empty($row['correct_answer_json'])) {
            $decoded = json_decode($row['correct_answer_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = null;
            }
        }

        if ($type === 'mcq_single') {
            if (is_array($decoded)) {
                $decoded = $decoded[0] ?? null;
            }
            if (is_string($decoded) && $decoded !== '') {
                return [trim($decoded), null];
            }
            if (!empty($row['correct_answer'])) {
                return [trim($row['correct_answer']), null];
            }
            if (!empty($row['correct_answers'])) {
                $values = array_filter(array_map('trim', preg_split('/[|;,]+/', $row['correct_answers'])));
                return [reset($values) ?: null, null];
            }
            return [null, null];
        }

        if ($type === 'mcq_multiple') {
            if (is_array($decoded)) {
                $filtered = array_values(array_filter($decoded, fn($value) => $value !== null && $value !== ''));
                if ($filtered) {
                    return [null, $filtered];
                }
            }
            if (!empty($row['correct_answers'])) {
                $values = array_filter(array_map('trim', preg_split('/[|;,]+/', $row['correct_answers'])));
                return [null, $values ?: null];
            }
        }

        return [null, null];
    }

    /**
     * Parse matching pairs from either JSON or pair columns
     */
    protected function parsePairs(array $row): ?array
    {
        if (!empty($row['pairs_json'])) {
            $decoded = json_decode($row['pairs_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // normalize shape [{left,right},...]
                $normalized = [];
                foreach ($decoded as $pair) {
                    if (!empty($pair['left']) || !empty($pair['right'])) {
                        $normalized[] = [
                            'left' => trim((string)($pair['left'] ?? '')),
                            'right' => trim((string)($pair['right'] ?? '')),
                        ];
                    }
                }
                return $normalized ?: null;
            }
        }

        $pairs = [];
        for ($i = 1; $i <= 6; $i++) {
            $leftKey = "pair{$i}_left";
            $rightKey = "pair{$i}_right";
            $left = trim((string)($row[$leftKey] ?? ''));
            $right = trim((string)($row[$rightKey] ?? ''));
            if ($left !== '' || $right !== '') {
                $pairs[] = ['left' => $left, 'right' => $right];
            }
        }
        return $pairs ?: null;
    }

    /**
     * Parse comma separated tags
     */
    protected function parseTags(array $row): ?array
    {
        if (empty($row['tags'])) {
            return null;
        }

        $tags = array_filter(array_map('trim', explode(',', $row['tags'])));
        return $tags ?: null;
    }

    /**
     * Normalize the points value from user input:
     * - Accepts null/empty -> returns null (will default later)
     * - Handles comma decimals and stray characters
     */
    protected function normalizePoints($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (is_string($value)) {
            // Replace comma with dot, strip non-numeric except dot and minus
            $clean = preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', $value));
            if ($clean === '' || $clean === '.' || $clean === '-.') {
                return null;
            }
            if (is_numeric($clean)) {
                return (float) $clean;
            }
        }
        return null;
    }
}
