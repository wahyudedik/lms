<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Display forum index (list all categories).
     */
    public function index()
    {
        $categories = ForumCategory::active()
            ->ordered()
            ->withCount('threads')
            ->get();

        // Latest threads across all categories
        $latestThreads = ForumThread::with(['user', 'category', 'lastReplyUser'])
            ->latestActivity()
            ->limit(5)
            ->get();

        // Popular threads (most replies)
        $popularThreads = ForumThread::with(['user', 'category'])
            ->popular()
            ->limit(5)
            ->get();

        // Forum statistics
        $stats = [
            'total_threads' => ForumThread::count(),
            'total_replies' => ForumReply::count(),
            'total_categories' => $categories->count(),
        ];

        return view('forum.index', compact('categories', 'latestThreads', 'popularThreads', 'stats'));
    }

    /**
     * Display threads in a category.
     */
    public function category($slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();

        $threads = ForumThread::where('category_id', $category->id)
            ->with(['user', 'lastReplyUser'])
            ->when(request('search'), function ($query) {
                $query->search(request('search'));
            })
            ->when(request('sort') === 'popular', function ($query) {
                $query->popular();
            }, function ($query) {
                $query->latestActivity();
            })
            ->paginate(20);

        return view('forum.category', compact('category', 'threads'));
    }

    /**
     * Display a thread with its replies.
     */
    public function show($categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)
            ->with(['user', 'category'])
            ->firstOrFail();

        // Increment views
        $thread->incrementViews();

        // Get replies with nested structure
        $replies = ForumReply::where('thread_id', $thread->id)
            ->whereNull('parent_id')
            ->with(['user', 'children.user', 'children.children.user'])
            ->withCount('children')
            ->orderBy('is_solution', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('forum.thread', compact('thread', 'replies'));
    }

    /**
     * Show form to create a new thread.
     */
    public function create($categorySlug = null)
    {
        $categories = ForumCategory::active()->ordered()->get();
        $selectedCategory = $categorySlug
            ? ForumCategory::where('slug', $categorySlug)->first()
            : null;

        return view('forum.create-thread', compact('categories', 'selectedCategory'));
    }

    /**
     * Store a new thread.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $thread = ForumThread::create([
            'category_id' => $validated['category_id'],
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'last_activity_at' => now(),
        ]);

        return redirect()
            ->route('forum.thread', [$thread->category->slug, $thread->slug])
            ->with('success', 'Thread created successfully!');
    }

    /**
     * Show form to edit a thread.
     */
    public function edit($categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();

        // Check permission
        if ($thread->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $categories = ForumCategory::active()->ordered()->get();
        $selectedCategory = $thread->category;

        return view('forum.create-thread', compact('thread', 'categories', 'selectedCategory'));
    }

    /**
     * Update a thread.
     */
    public function update(Request $request, $categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();

        // Check permission
        if ($thread->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $thread->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('forum.thread', [$thread->category->slug, $thread->slug])
            ->with('success', 'Thread updated successfully!');
    }

    /**
     * Delete a thread.
     */
    public function destroy($categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();

        // Check permission
        if ($thread->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $categorySlug = $thread->category->slug;
        $thread->delete();

        return redirect()
            ->route('forum.category', $categorySlug)
            ->with('success', 'Thread deleted successfully!');
    }

    /**
     * Store a reply to a thread.
     */
    public function storeReply(Request $request, $categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();

        // Check if thread is locked
        if ($thread->is_locked && !Auth::user()->isAdmin()) {
            return back()->with('error', 'This thread is locked!');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        $reply = ForumReply::create([
            'thread_id' => $thread->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Reply posted successfully!');
    }

    /**
     * Update a reply.
     */
    public function updateReply(Request $request, ForumReply $reply)
    {
        // Check permission
        if ($reply->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $reply->update(['content' => $validated['content']]);

        return back()->with('success', 'Reply updated successfully!');
    }

    /**
     * Delete a reply.
     */
    public function destroyReply(ForumReply $reply)
    {
        // Check permission
        if ($reply->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Get thread info before deleting
        $thread = $reply->thread;
        $category = $thread->category;

        $reply->delete();

        // If AJAX request, return JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply deleted successfully!'
            ]);
        }

        // Otherwise redirect
        return redirect()->route('forum.thread', [$category->slug, $thread->slug])
            ->with('success', 'Reply deleted successfully!');
    }

    /**
     * Toggle like on a thread or reply.
     */
    public function toggleLike(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:thread,reply',
            'id' => 'required|integer',
        ]);

        $model = $validated['type'] === 'thread'
            ? ForumThread::findOrFail($validated['id'])
            : ForumReply::findOrFail($validated['id']);

        $liked = $model->toggleLike(Auth::user());

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $model->likes_count,
        ]);
    }

    /**
     * Toggle pin on a thread (Admin/Guru only).
     */
    public function togglePin($categorySlug, $threadSlug)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();
        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return back()->with('success', $thread->is_pinned ? 'Thread pinned!' : 'Thread unpinned!');
    }

    /**
     * Toggle lock on a thread (Admin/Guru only).
     */
    public function toggleLock($categorySlug, $threadSlug)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();
        $thread->update(['is_locked' => !$thread->is_locked]);

        return back()->with('success', $thread->is_locked ? 'Thread locked!' : 'Thread unlocked!');
    }

    /**
     * Mark reply as solution (Thread owner or Admin/Guru).
     */
    public function markSolution($replyId)
    {
        $reply = ForumReply::findOrFail($replyId);
        $thread = $reply->thread;

        // Check permission
        if ($thread->user_id !== Auth::id() && !Auth::user()->isAdmin() && !Auth::user()->isGuru()) {
            abort(403, 'Unauthorized');
        }

        $reply->markAsSolution();

        return back()->with('success', 'Reply marked as solution!');
    }

    /**
     * Search forum.
     */
    public function search(Request $request)
    {
        $search = $request->get('q');

        $threads = ForumThread::with(['user', 'category'])
            ->search($search)
            ->latestActivity()
            ->paginate(20);

        return view('forum.search', compact('threads', 'search'));
    }
}
