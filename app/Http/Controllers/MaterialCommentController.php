<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialComment;
use Illuminate\Http\Request;

class MaterialCommentController extends Controller
{
    public function store(Request $request, Material $material)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:material_comments,id',
        ]);

        $validated['material_id'] = $material->id;
        $validated['user_id'] = auth()->id();

        $comment = MaterialComment::create($validated);

        // Notify material owner (new comment) or parent comment owner (reply)
        if ($comment->parent_id) {
            $recipient = $comment->parent->user;
        } else {
            $recipient = $material->creator;
        }

        if ($recipient && $recipient->id !== auth()->id()) {
            $recipient->notify(new \App\Notifications\MaterialCommentReceived($comment));
        }

        // Parse mentions and notify mentioned users
        $mentionParser = app(\App\Services\MentionParser::class);
        $mentionedUsers = $mentionParser->parse(
            $comment->comment,
            'material_comment',
            auth()->user()->school_class_id,
            auth()->id()
        );

        foreach ($mentionedUsers as $mentionedUser) {
            // Generate role-specific URL for the mentioned user
            $actionUrl = $mentionedUser->getNotificationUrl('material', $material->id);

            $mentionedUser->notify(new \App\Notifications\UserMentioned(
                auth()->user(),
                'material',
                $material->title,
                \Illuminate\Support\Str::limit($comment->comment, 100),
                $actionUrl
            ));
        }

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function update(Request $request, MaterialComment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        return back()->with('success', 'Komentar berhasil diperbarui!');
    }

    public function destroy(MaterialComment $comment)
    {
        // Check if user can delete the comment
        if (!$comment->canDelete(auth()->user())) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }
}
