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
            background: #f5f5f5;
            padding: 20px;
        }

        .certificate-wrapper {
            background: #ffffff;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        /* Minimal top accent */
        .accent-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #111827;
        }

        /* Content */
        .content {
            position: absolute;
            top: 60px;
            left: 80px;
            right: 80px;
            bottom: 50px;
        }

        /* Header - left aligned */
        .header {
            margin-bottom: 40px;
        }

        .institution-name {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .certificate-title {
            font-size: 48px;
            color: #111827;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 4px;
        }

        .certificate-subtitle {
            font-size: 14px;
            color: #6b7280;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Body */
        .body {
            margin-top: 30px;
        }

        .preamble {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .student-name {
            font-size: 38px;
            color: #111827;
            font-weight: 900;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .course-label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }

        .course-title {
            font-size: 20px;
            color: #374151;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Details - horizontal layout */
        .details-row {
            margin: 25px 0;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }

        .details-table {
            border-collapse: collapse;
        }

        .details-table td {
            padding: 0 30px 0 0;
            vertical-align: top;
        }

        .detail-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .detail-value {
            font-size: 15px;
            color: #111827;
            font-weight: 800;
        }

        /* Grade - simple */
        .grade-box {
            display: inline-block;
            border: 2px solid #111827;
            padding: 8px 20px;
            margin-right: 20px;
        }

        .grade-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .grade-value {
            font-size: 28px;
            color: #111827;
            font-weight: 900;
        }

        /* Signatures */
        .signatures {
            position: absolute;
            bottom: 40px;
            left: 80px;
            right: 80px;
        }

        .sig-table {
            border-collapse: collapse;
        }

        .sig-table td {
            padding: 0 40px 0 0;
            vertical-align: bottom;
        }

        .sig-line {
            width: 140px;
            height: 1px;
            background: #111827;
            margin-bottom: 8px;
        }

        .sig-name {
            font-size: 12px;
            font-weight: 800;
            color: #111827;
        }

        .sig-title {
            font-size: 10px;
            color: #9ca3af;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 15px;
            left: 80px;
            right: 80px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            font-size: 9px;
            color: #d1d5db;
            letter-spacing: 1px;
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
        <div class="accent-bar"></div>

        <div class="content">
            <div class="header">
                <div class="institution-name">{{ config('certificate.institution.name', 'Learning Management System') }}
                </div>
                <div class="certificate-title">Sertifikat</div>
                <div class="certificate-subtitle">Penyelesaian Kursus</div>
            </div>

            <div class="body">
                <p class="preamble">Diberikan kepada</p>
                <div class="student-name">{{ $certificate->student_name }}</div>

                <p class="course-label">Kursus yang diselesaikan</p>
                <div class="course-title">{{ $certificate->course_title }}</div>

                <div class="details-row">
                    <table class="details-table">
                        <tr>
                            @if ($certificate->grade)
                                <td>
                                    <div class="grade-box">
                                        <div class="grade-label">Nilai</div>
                                        <div class="grade-value">{{ $certificate->grade }}</div>
                                    </div>
                                </td>
                            @endif
                            @if ($certificate->final_score)
                                <td>
                                    <div class="detail-label">Skor Akhir</div>
                                    <div class="detail-value">{{ $certificate->final_score }}%</div>
                                </td>
                            @endif
                            <td>
                                <div class="detail-label">Tanggal Selesai</div>
                                <div class="detail-value">{{ $certificate->completion_date->translatedFormat('d F Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="detail-label">Diterbitkan</div>
                                <div class="detail-value">{{ $certificate->issue_date->translatedFormat('d F Y') }}
                                </div>
                            </td>
                            @if (isset($certificate->metadata['total_hours']) && $certificate->metadata['total_hours'] > 0)
                                <td>
                                    <div class="detail-label">Durasi</div>
                                    <div class="detail-value">{{ $certificate->metadata['total_hours'] }} Jam</div>
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
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">{{ $certificate->certificate_number }}</td>
                    <td class="footer-right">{{ $certificate->verification_url }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
