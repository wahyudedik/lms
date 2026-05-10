<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 100%;
            height: 100vh;
            background: #0f172a;
            padding: 20px;
        }

        .certificate-wrapper {
            background: #ffffff;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }

        /* Left accent strip */
        .accent-left {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 18px;
            background: #3b82f6;
        }

        .accent-left-inner {
            position: absolute;
            top: 0;
            left: 18px;
            bottom: 0;
            width: 6px;
            background: #8b5cf6;
        }

        /* Top right decorative circle */
        .deco-circle {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 30px solid #f1f5f9;
        }

        /* Bottom left decorative circle */
        .deco-circle-bl {
            position: absolute;
            bottom: -80px;
            left: 60px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 25px solid #f8fafc;
        }

        /* Content */
        .content {
            position: absolute;
            top: 40px;
            left: 60px;
            right: 50px;
            bottom: 40px;
        }

        /* Header */
        .header {
            text-align: left;
            margin-bottom: 20px;
        }

        .institution-name {
            font-size: 12px;
            color: #64748b;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .certificate-title {
            font-size: 38px;
            color: #1e293b;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .certificate-subtitle {
            font-size: 14px;
            color: #3b82f6;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .header-line {
            width: 80px;
            height: 4px;
            background: #3b82f6;
            margin-top: 12px;
        }

        /* Body */
        .body {
            margin-top: 25px;
        }

        .preamble {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .student-name {
            font-size: 34px;
            color: #1e293b;
            font-weight: 900;
            margin: 6px 0 12px;
            letter-spacing: 1px;
        }

        .completion-text {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .course-title {
            font-size: 20px;
            color: #3b82f6;
            font-weight: 700;
            margin-bottom: 15px;
        }

        /* Stats boxes */
        .stats-row {
            margin: 20px 0;
        }

        .stats-table {
            border-collapse: collapse;
        }

        .stats-table td {
            padding: 10px 25px 10px 0;
            vertical-align: top;
        }

        .stat-label {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 16px;
            color: #1e293b;
            font-weight: 800;
        }

        .stat-value-accent {
            font-size: 16px;
            color: #3b82f6;
            font-weight: 800;
        }

        /* Grade badge */
        .grade-badge {
            display: inline-block;
            width: 55px;
            height: 55px;
            border: 3px solid #3b82f6;
            border-radius: 50%;
            line-height: 49px;
            font-size: 24px;
            font-weight: 900;
            color: #3b82f6;
            text-align: center;
        }

        /* Signatures */
        .signatures {
            position: absolute;
            bottom: 30px;
            left: 60px;
            right: 50px;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-table td {
            padding: 0 20px;
            vertical-align: bottom;
        }

        .sig-line {
            width: 150px;
            height: 2px;
            background: #cbd5e1;
            margin-bottom: 8px;
        }

        .sig-name {
            font-size: 12px;
            font-weight: 800;
            color: #1e293b;
        }

        .sig-title {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 12px;
            left: 60px;
            right: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            font-size: 9px;
            color: #94a3b8;
            vertical-align: middle;
        }

        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="certificate-wrapper">
        <div class="accent-left"></div>
        <div class="accent-left-inner"></div>
        <div class="deco-circle"></div>
        <div class="deco-circle-bl"></div>

        <div class="content">
            <div class="header">
                <div class="institution-name">{{ config('certificate.institution.name', 'Learning Management System') }}
                </div>
                <div class="certificate-title">Sertifikat</div>
                <div class="certificate-subtitle">Penyelesaian Kursus</div>
                <div class="header-line"></div>
            </div>

            <div class="body">
                <p class="preamble">Diberikan kepada</p>
                <div class="student-name">{{ $certificate->student_name }}</div>
                <p class="completion-text">Atas keberhasilan menyelesaikan kursus</p>
                <div class="course-title">&ldquo;{{ $certificate->course_title }}&rdquo;</div>

                <div class="stats-row">
                    <table class="stats-table">
                        <tr>
                            @if ($certificate->grade)
                                <td>
                                    <div class="stat-label">Nilai</div>
                                    <div class="grade-badge">{{ $certificate->grade }}</div>
                                </td>
                            @endif
                            @if ($certificate->final_score)
                                <td>
                                    <div class="stat-label">Skor Akhir</div>
                                    <div class="stat-value-accent">{{ $certificate->final_score }}%</div>
                                </td>
                            @endif
                            <td>
                                <div class="stat-label">Tanggal Selesai</div>
                                <div class="stat-value">{{ $certificate->completion_date->translatedFormat('d F Y') }}
                                </div>
                            </td>
                            @if (isset($certificate->metadata['total_hours']) && $certificate->metadata['total_hours'] > 0)
                                <td>
                                    <div class="stat-label">Durasi</div>
                                    <div class="stat-value">{{ $certificate->metadata['total_hours'] }} Jam</div>
                                </td>
                            @endif
                        </tr>
                    </table>
                </div>
            </div>

            <div class="signatures">
                <table class="sig-table">
                    <tr>
                        <td>
                            <div class="sig-line"></div>
                            <div class="sig-name">{{ $certificate->instructor_name ?? 'Pengajar' }}</div>
                            <div class="sig-title">Pengajar Kursus</div>
                        </td>
                        <td>
                            <div class="sig-line"></div>
                            <div class="sig-name">{{ config('certificate.institution.director', 'Direktur') }}</div>
                            <div class="sig-title">{{ config('certificate.institution.name', 'LMS Platform') }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="stat-label">Diterbitkan</div>
                            <div class="stat-value">{{ $certificate->issue_date->translatedFormat('d F Y') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">No: {{ $certificate->certificate_number }}</td>
                    <td class="footer-right">Verifikasi: {{ $certificate->verification_url }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
