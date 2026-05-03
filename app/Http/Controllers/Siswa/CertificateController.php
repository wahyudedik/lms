<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('course.instructor')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->paginate(12);

        // Statistics
        $stats = [
            'completed_courses' => Auth::user()->enrollments()
                ->whereHas('certificate')
                ->count(),
            'average_score' => Certificate::where('user_id', Auth::id())
                ->avg('final_score') ?? 0,
        ];

        return view('siswa.certificates.index', compact('certificates', 'stats'));
    }

    public function show(Certificate $certificate)
    {
        // Check if certificate belongs to current user
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }

        $certificate->load('course.instructor');

        return view('siswa.certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        // Check if certificate belongs to current user
        if ($certificate->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke sertifikat ini.');
        }

        $certificate->load('course.instructor');
        $user = Auth::user();

        // Generate PDF
        $pdf = Pdf::loadView('certificates.template', compact('certificate', 'user'));

        $filename = 'Certificate-' . $certificate->certificate_number . '.pdf';

        return $pdf->download($filename);
    }
}
