<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\InformationCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformationCardController extends Controller
{
    /**
     * Get the target role based on the current user's role.
     */
    private function getTargetRole(): string
    {
        return auth()->user()->role === 'dosen' ? 'mahasiswa' : 'siswa';
    }

    /**
     * Display a listing of information cards.
     */
    public function index(Request $request)
    {
        $query = InformationCard::with('creator')
            ->where('created_by', auth()->id());

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $cards = $query->orderByDesc('created_at')->paginate(15);

        return view('guru.information-cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new information card.
     */
    public function create()
    {
        $targetRole = $this->getTargetRole();
        $users = User::where('is_active', true)
            ->where('role', $targetRole)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('guru.information-cards.create', compact('targetRole', 'users'));
    }

    /**
     * Store a newly created information card.
     */
    public function store(Request $request)
    {
        $targetRole = $this->getTargetRole();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'card_type' => ['required', 'in:info,warning,success,danger'],
            'icon' => ['nullable', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'target_user_ids' => ['nullable', 'array'],
            'target_user_ids.*' => ['exists:users,id'],
            'schedule_type' => ['required', 'in:always,date_range,daily'],
            'start_date' => ['required_if:schedule_type,date_range', 'nullable', 'date'],
            'end_date' => ['required_if:schedule_type,date_range', 'nullable', 'date', 'after_or_equal:start_date'],
            'daily_start_time' => ['nullable', 'date_format:H:i'],
            'daily_end_time' => ['nullable', 'date_format:H:i'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $targetUserIds = !empty($validated['target_user_ids']) ? $validated['target_user_ids'] : null;

        $data = [
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'card_type' => $validated['card_type'],
            'icon' => $validated['icon'] ?? 'fas fa-info-circle',
            'video_url' => $validated['video_url'] ?? null,
            'target_roles' => [$targetRole],
            'target_user_ids' => $targetUserIds,
            'schedule_type' => $validated['schedule_type'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'daily_start_time' => $validated['daily_start_time'] ?? null,
            'daily_end_time' => $validated['daily_end_time'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('information-cards', 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_size'] = $file->getSize();
        }

        InformationCard::create($data);

        return redirect()->route(auth()->user()->getRolePrefix() . '.information-cards.index')
            ->with('success', __('Information card created successfully.'));
    }

    /**
     * Show the form for editing the specified information card.
     */
    public function edit(InformationCard $informationCard)
    {
        if ($informationCard->created_by !== auth()->id()) {
            abort(403);
        }

        $targetRole = $this->getTargetRole();
        $users = User::where('is_active', true)
            ->where('role', $targetRole)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('guru.information-cards.edit', compact('informationCard', 'targetRole', 'users'));
    }

    /**
     * Update the specified information card.
     */
    public function update(Request $request, InformationCard $informationCard)
    {
        if ($informationCard->created_by !== auth()->id()) {
            abort(403);
        }

        $targetRole = $this->getTargetRole();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'card_type' => ['required', 'in:info,warning,success,danger'],
            'icon' => ['nullable', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'target_user_ids' => ['nullable', 'array'],
            'target_user_ids.*' => ['exists:users,id'],
            'schedule_type' => ['required', 'in:always,date_range,daily'],
            'start_date' => ['required_if:schedule_type,date_range', 'nullable', 'date'],
            'end_date' => ['required_if:schedule_type,date_range', 'nullable', 'date', 'after_or_equal:start_date'],
            'daily_start_time' => ['nullable', 'date_format:H:i'],
            'daily_end_time' => ['nullable', 'date_format:H:i'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $targetUserIds = !empty($validated['target_user_ids']) ? $validated['target_user_ids'] : null;

        $data = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'card_type' => $validated['card_type'],
            'icon' => $validated['icon'] ?? 'fas fa-info-circle',
            'video_url' => $validated['video_url'] ?? null,
            'target_roles' => [$targetRole],
            'target_user_ids' => $targetUserIds,
            'schedule_type' => $validated['schedule_type'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'daily_start_time' => $validated['daily_start_time'] ?? null,
            'daily_end_time' => $validated['daily_end_time'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        // Handle attachment removal
        if ($request->boolean('remove_attachment') && $informationCard->attachment_path) {
            Storage::disk('public')->delete($informationCard->attachment_path);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
            $data['attachment_size'] = null;
        }

        // Handle new file upload
        if ($request->hasFile('attachment')) {
            if ($informationCard->attachment_path) {
                Storage::disk('public')->delete($informationCard->attachment_path);
            }
            $file = $request->file('attachment');
            $path = $file->store('information-cards', 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_size'] = $file->getSize();
        }

        $informationCard->update($data);

        return redirect()->route(auth()->user()->getRolePrefix() . '.information-cards.index')
            ->with('success', __('Information card updated successfully.'));
    }

    /**
     * Remove the specified information card.
     */
    public function destroy(InformationCard $informationCard)
    {
        if ($informationCard->created_by !== auth()->id()) {
            abort(403);
        }

        if ($informationCard->attachment_path) {
            Storage::disk('public')->delete($informationCard->attachment_path);
        }

        $informationCard->delete();

        return redirect()->route(auth()->user()->getRolePrefix() . '.information-cards.index')
            ->with('success', __('Information card deleted successfully.'));
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(InformationCard $informationCard)
    {
        if ($informationCard->created_by !== auth()->id()) {
            abort(403);
        }

        $informationCard->update(['is_active' => !$informationCard->is_active]);

        $status = $informationCard->is_active ? __('activated') : __('deactivated');

        return redirect()->route(auth()->user()->getRolePrefix() . '.information-cards.index')
            ->with('success', __('Information card :status successfully.', ['status' => $status]));
    }
}
