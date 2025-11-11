<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Display a listing of user's certificates
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $certificates = Certificate::byUser($user->id)
            ->with(['course', 'enrollment'])
            ->latest('issue_date')
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Show specific certificate
     */
    public function show(string $certificateNumber)
    {
        $certificate = Certificate::byCertificateNumber($certificateNumber)
            ->with(['user', 'course', 'enrollment'])
            ->firstOrFail();

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Download certificate as PDF
     */
    public function download(string $certificateNumber)
    {
        $certificate = Certificate::byCertificateNumber($certificateNumber)
            ->with(['user', 'course'])
            ->firstOrFail();

        // Check if user has permission to download
        if (!$this->canAccessCertificate($certificate)) {
            abort(403, 'You do not have permission to download this certificate.');
        }

        return $this->certificateService->downloadPDF($certificate);
    }

    /**
     * View certificate PDF in browser
     */
    public function view(string $certificateNumber)
    {
        $certificate = Certificate::byCertificateNumber($certificateNumber)
            ->with(['user', 'course'])
            ->firstOrFail();

        return $this->certificateService->streamPDF($certificate);
    }

    /**
     * Verify certificate authenticity
     */
    public function verify(Request $request, ?string $certificateNumber = null)
    {
        $number = $certificateNumber ?? $request->input('certificate_number');

        if (!$number) {
            return view('certificates.verify');
        }

        $certificate = $this->certificateService->verify($number);

        return view('certificates.verify', compact('certificate', 'number'));
    }

    /**
     * Generate certificate for enrollment
     */
    public function generate(Enrollment $enrollment)
    {
        // Check authorization
        if (!$this->canGenerateCertificate($enrollment)) {
            abort(403, 'You do not have permission to generate certificate for this enrollment.');
        }

        try {
            $certificate = $this->certificateService->generateForEnrollment($enrollment);

            return redirect()
                ->route('certificates.show', $certificate->certificate_number)
                ->with('success', 'Certificate generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: List all certificates
     */
    public function adminIndex(Request $request)
    {
        $query = Certificate::with(['user', 'course']);

        // Filters
        if ($request->filled('status')) {
            if ($request->status === 'valid') {
                $query->valid();
            } elseif ($request->status === 'revoked') {
                $query->revoked();
            }
        }

        if ($request->filled('course_id')) {
            $query->byCourse($request->course_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('certificate_number', 'like', '%' . $request->search . '%')
                    ->orWhere('student_name', 'like', '%' . $request->search . '%')
                    ->orWhere('course_title', 'like', '%' . $request->search . '%');
            });
        }

        $certificates = $query->latest()->paginate(20);
        $statistics = $this->certificateService->getStatistics();

        return view('admin.certificates.index', compact('certificates', 'statistics'));
    }

    /**
     * Admin: Revoke certificate
     */
    public function revoke(Request $request, Certificate $certificate)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $certificate->revoke($request->reason);

        return back()->with('success', 'Certificate revoked successfully.');
    }

    /**
     * Admin: Restore certificate
     */
    public function restore(Certificate $certificate)
    {
        $certificate->restore();

        return back()->with('success', 'Certificate restored successfully.');
    }

    /**
     * Admin: Generate missing certificates
     */
    public function generateMissing()
    {
        $count = $this->certificateService->generateMissing();

        return back()->with('success', "Generated {$count} missing certificates.");
    }

    /**
     * API: Get user certificates
     */
    public function apiIndex(Request $request)
    {
        $user = $request->user();

        $certificates = Certificate::byUser($user->id)
            ->with(['course'])
            ->latest('issue_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates,
        ]);
    }

    /**
     * API: Verify certificate
     */
    public function apiVerify(Request $request)
    {
        $request->validate([
            'certificate_number' => 'required|string',
        ]);

        $certificate = $this->certificateService->verify($request->certificate_number);

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found or invalid',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $certificate->load(['user', 'course']),
        ]);
    }

    /**
     * Check if current user can access certificate
     */
    protected function canAccessCertificate(Certificate $certificate): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Owner can access
        if ($certificate->user_id === $user->id) {
            return true;
        }

        // Admin can access
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor of the course can access
        if ($user->hasRole('instructor') && $certificate->course->instructor_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if current user can generate certificate
     */
    protected function canGenerateCertificate(Enrollment $enrollment): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Student can generate their own
        if ($enrollment->user_id === $user->id) {
            return true;
        }

        // Admin can generate
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor of the course can generate
        if ($user->hasRole('instructor') && $enrollment->course->instructor_id === $user->id) {
            return true;
        }

        return false;
    }
}
