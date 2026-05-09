<?php

namespace App\Services;

use App\Models\Assignment;
use Illuminate\Http\UploadedFile;

class FileValidationService
{
    /**
     * Maximum file size in bytes (10 MB).
     */
    public const MAX_FILE_SIZE = 10485760;

    /**
     * All supported file types for assignment submissions.
     */
    public const SUPPORTED_FILE_TYPES = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi'];

    /**
     * Validate an uploaded file against the assignment's constraints.
     *
     * @param  UploadedFile  $file
     * @param  Assignment  $assignment
     * @return array{valid: bool, error: string|null}
     */
    public function validate(UploadedFile $file, Assignment $assignment): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = $this->getAllowedExtensions($assignment);

        if (!in_array($extension, $allowedExtensions)) {
            $types = implode(', ', $allowedExtensions);

            return [
                'valid' => false,
                'error' => "Tipe file tidak diizinkan. Tipe yang diizinkan: {$types}",
            ];
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return [
                'valid' => false,
                'error' => 'Ukuran file maksimal 10 MB',
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }

    /**
     * Get the allowed file extensions for an assignment.
     *
     * Returns the assignment's configured types, or all supported types if none specified.
     *
     * @param  Assignment  $assignment
     * @return array<string>
     */
    public function getAllowedExtensions(Assignment $assignment): array
    {
        return $assignment->allowed_file_types ?? self::SUPPORTED_FILE_TYPES;
    }

    /**
     * Get the maximum allowed file size in bytes.
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return self::MAX_FILE_SIZE;
    }
}
