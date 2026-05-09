<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class MentionParser
{
    /**
     * Parse @username mentions from content and return the matching User models.
     *
     * @param string   $content             The content to parse for @username patterns
     * @param string   $context             'forum' or 'material_comment'
     * @param int|null $authorSchoolClassId The school_class_id of the author (used for material_comment scope)
     * @param int      $authorId            The author's user ID (excluded from results to prevent self-mention)
     * @return Collection<int, User>
     */
    public function parse(
        string $content,
        string $context,
        ?int $authorSchoolClassId,
        int $authorId
    ): Collection {
        // 1. Extract all @username patterns from content
        preg_match_all('/@(\w+)/', $content, $matches);

        if (empty($matches[1])) {
            return collect();
        }

        // 2. Deduplicate the username list
        $usernames = array_unique($matches[1]);

        // 3. Apply context-specific query rules
        if ($context === 'material_comment') {
            // If author has no school class, no mentions are valid
            if ($authorSchoolClassId === null) {
                return collect();
            }

            // Only users in the same school class can be mentioned
            return User::whereIn('username', $usernames)
                ->where('school_class_id', $authorSchoolClassId)
                ->where('id', '!=', $authorId)
                ->get();
        }

        // 4. Forum context: any registered user can be mentioned
        return User::whereIn('username', $usernames)
            ->where('id', '!=', $authorId)
            ->get();
    }
}
