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
            font-family: 'Georgia', 'Times New Roman', serif;
            width: 100%;
            height: 100vh;
            background: #1a237e;
            padding: 25px;
        }

        .certificate-wrapper {
            background: #ffffff;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        /* Decorative top/bottom bars */
        .bar-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 12px;
            background: #1a237e;
        }

        .bar-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 12px;
            background: #1a237e;
        }

        /* Inner border */
        .inner-frame {
            position: absolute;
            top: 25px;
            left: 25px;
            right: 25px;
            bottom: 25px;
            border: 2px solid #c5a028;
        }

        /* Corner ornaments */
        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: #1a237e;
            border-style: solid;
        }

        .corner-tl {
            top: 35px;
            left: 35px;
            border-width: 4px 0 0 4px;
        }

        .corner-tr {
            top: 35px;
            right: 35px;
            border-width: 4px 4px 0 0;
        }

        .corner-bl {
            bottom: 35px;
            left: 35px;
            border-width: 0 0 4px 4px;
        }

        .corner-br {
            bottom: 35px;
            right: 35px;
            border-width: 0 4px 4px 0;
        }

        /* Content area */
        .content {
            position: absolute;
            top: 50px;
            left: 60px;
            right: 60px;
            bottom: 50px;
            text-align: center;
        }

        /* Header */
        .institution-name {
            font-size: 14px;
            color: #555;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .certificate-title {
            font-size: 42px;
            color: #1a237e;
            font-weight: bold;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .certificate-subtitle {
            font-size: 16px;
            color: #c5a028;
            letter-spacing: 4px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .divider {
            width: 120px;
            height: 3px;
            background: #c5a028;
            margin: 18px auto;
        }

        /* Body */
        .preamble {
            font-size: 14px;
            color: #555;
            margin-bottom: 12px;
            font-style: italic;
        }

        .student-name {
            font-size: 36px;
            color: #1a237e;
            font-weight: bold;
            margin: 10px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #c5a028;
            display: inline-block;
            letter-spacing: 2px;
        }

        .completion-text {
            font-size: 14px;
            color: #555;
            margin: 12px 0;
            font-style: italic;
        }

        .course-title {
            font-size: 22px;
            color: #333;
            font-weight: bold;
            margin: 8px 0;
        }

        /* Grade circle */
        .grade-section {
            margin: 15px 0;
        }

        .grade-circle {
            display: inline-block;
            width: 60px;
            height: 60px;
            border: 3px solid #c5a028;
            border-radius: 50%;
            line-height: 54px;
            font-size: 28px;
            font-weight: bold;
            color: #1a237e;
            text-align: center;
        }

        /* Details */
        .details-table {
            margin: 15px auto;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 4px 15px;
            font-size: 12px;
        }

        .details-table .label {
            color: #777;
            text-align: right;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .details-table .value {
            color: #333;
            text-align: left;
            font-weight: bold;
        }

        /* Footer signatures */
        .signatures {
            position: absolute;
            bottom: 40px;
            left: 80px;
            right: 80px;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-table td {
            text-align: center;
            padding: 0 30px;
            vertical-align: bottom;
        }

        .sig-line {
            width: 160px;
            height: 2px;
            background: #333;
            margin: 0 auto 8px;
        }

        .sig-name {
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }

        .sig-title {
            font-size: 11px;
            color: #777;
            font-style: italic;
        }

        /* Certificate number & verification */
        .cert-number {
            position: absolute;
            bottom: 15px;
            left: 60px;
            font-size: 9px;
            color: #999;
            letter-spacing: 1px;
        }

        .verification {
            position: absolute;
            bottom: 15px;
            right: 60px;
            font-size: 9px;
            color: #999;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="certificate-wrapper">
        <div class="bar-top"></div>
        <div class="bar-bottom"></div>
        <div class="inner-frame"></div>
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="content">
            <div class="institution-name">{{ config('certificate.institution.name', 'Learning Management System') }}
            </div>
            <div class="certificate-title">Sertifikat</div>
            <div class="certificate-subtitle">Penyelesaian Kursus</div>
            <div class="divider"></div>

            <p class="preamble">Dengan ini menyatakan bahwa</p>

            <div class="student-name">{{ $certificate->student_name }}</div>

            <p class="completion-text">telah berhasil menyelesaikan kursus</p>

            <div class="course-title">&ldquo;{{ $certificate->course_title }}&rdquo;</div>

            @if ($certificate->grade)
                <div class="grade-section">
                    <div class="grade-circle">{{ $certificate->grade }}</div>
                </div>
            @endif

            <table class="details-table">
                <tr>
                    <td class="label">Tanggal Selesai</td>
                    <td class="value">{{ $certificate->completion_date->translatedFormat('d F Y') }}</td>
                </tr>
                @if ($certificate->final_score)
                    <tr>
                        <td class="label">Nilai Akhir</td>
                        <td class="value">{{ $certificate->final_score }}%</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Tanggal Terbit</td>
                    <td class="value">{{ $certificate->issue_date->translatedFormat('d F Y') }}</td>
                </tr>
                @if (isset($certificate->metadata['total_hours']) && $certificate->metadata['total_hours'] > 0)
                    <tr>
                        <td class="label">Durasi</td>
                        <td class="value">{{ $certificate->metadata['total_hours'] }} Jam</td>
                    </tr>
                @endif
            </table>

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

        <div class="cert-number">No: {{ $certificate->certificate_number }}</div>
        <div class="verification">Verifikasi: {{ $certificate->verification_url }}</div>
    </div>
</body>

</html>
