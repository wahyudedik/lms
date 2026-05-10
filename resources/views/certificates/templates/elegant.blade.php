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
            background: #1a1a1a;
            padding: 20px;
        }

        .certificate-wrapper {
            background: #fffef8;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 8px solid #2c1810;
            box-shadow: inset 0 0 0 3px #c5a028;
        }

        /* Gold inner border */
        .gold-frame {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 2px solid #c5a028;
        }

        /* Corner ornaments using borders */
        .ornament {
            position: absolute;
            width: 50px;
            height: 50px;
        }

        .ornament-tl {
            top: 20px;
            left: 20px;
            border-top: 4px solid #c5a028;
            border-left: 4px solid #c5a028;
        }

        .ornament-tr {
            top: 20px;
            right: 20px;
            border-top: 4px solid #c5a028;
            border-right: 4px solid #c5a028;
        }

        .ornament-bl {
            bottom: 20px;
            left: 20px;
            border-bottom: 4px solid #c5a028;
            border-left: 4px solid #c5a028;
        }

        .ornament-br {
            bottom: 20px;
            right: 20px;
            border-bottom: 4px solid #c5a028;
            border-right: 4px solid #c5a028;
        }

        /* Content */
        .content {
            position: absolute;
            top: 45px;
            left: 70px;
            right: 70px;
            bottom: 45px;
            text-align: center;
        }

        /* Header */
        .institution-name {
            font-size: 13px;
            color: #6b5d52;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .certificate-title {
            font-size: 50px;
            color: #2c1810;
            font-weight: normal;
            font-style: italic;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .certificate-subtitle {
            font-size: 18px;
            color: #c5a028;
            letter-spacing: 5px;
            text-transform: uppercase;
            font-weight: normal;
        }

        .gold-divider {
            width: 200px;
            height: 2px;
            background: #c5a028;
            margin: 20px auto;
        }

        .gold-divider-thin {
            width: 100px;
            height: 1px;
            background: #c5a028;
            margin: 5px auto 20px;
        }

        /* Body */
        .preamble {
            font-size: 15px;
            color: #6b5d52;
            font-style: italic;
            margin-bottom: 10px;
        }

        .student-name {
            font-size: 40px;
            color: #2c1810;
            font-weight: normal;
            font-style: italic;
            margin: 10px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #c5a028;
            display: inline-block;
            letter-spacing: 2px;
        }

        .completion-text {
            font-size: 15px;
            color: #6b5d52;
            font-style: italic;
            margin: 12px 0;
        }

        .course-title {
            font-size: 24px;
            color: #2c1810;
            font-weight: bold;
            margin: 8px 0 15px;
            letter-spacing: 1px;
        }

        /* Grade */
        .grade-seal {
            display: inline-block;
            width: 65px;
            height: 65px;
            border: 3px solid #c5a028;
            border-radius: 50%;
            line-height: 59px;
            font-size: 30px;
            font-weight: bold;
            color: #2c1810;
            text-align: center;
            margin: 10px 0;
        }

        /* Details */
        .details-box {
            margin: 15px auto;
            max-width: 450px;
            border-top: 1px solid #c5a028;
            border-bottom: 1px solid #c5a028;
            padding: 12px 0;
        }

        .details-table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 4px 12px;
            font-size: 12px;
        }

        .details-table .label {
            color: #6b5d52;
            text-align: right;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 10px;
        }

        .details-table .value {
            color: #2c1810;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        /* Signatures */
        .signatures {
            position: absolute;
            bottom: 35px;
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
            height: 1px;
            background: #2c1810;
            margin: 0 auto 8px;
        }

        .sig-name {
            font-size: 13px;
            font-weight: bold;
            color: #2c1810;
        }

        .sig-title {
            font-size: 10px;
            color: #6b5d52;
            font-style: italic;
        }

        /* Footer */
        .cert-number {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>
    <div class="certificate-wrapper">
        <div class="gold-frame"></div>
        <div class="ornament ornament-tl"></div>
        <div class="ornament ornament-tr"></div>
        <div class="ornament ornament-bl"></div>
        <div class="ornament ornament-br"></div>

        <div class="content">
            <div class="institution-name">{{ config('certificate.institution.name', 'Learning Management System') }}
            </div>
            <div class="certificate-title">Sertifikat</div>
            <div class="certificate-subtitle">Penghargaan</div>
            <div class="gold-divider"></div>
            <div class="gold-divider-thin"></div>

            <p class="preamble">Dengan bangga diberikan kepada</p>

            <div class="student-name">{{ $certificate->student_name }}</div>

            <p class="completion-text">yang telah berhasil menyelesaikan kursus</p>

            <div class="course-title">&ldquo;{{ $certificate->course_title }}&rdquo;</div>

            @if ($certificate->grade)
                <div class="grade-seal">{{ $certificate->grade }}</div>
            @endif

            <div class="details-box">
                <table class="details-table">
                    <tr>
                        <td class="label">Tanggal Selesai</td>
                        <td class="value">{{ $certificate->completion_date->translatedFormat('d F Y') }}</td>
                        @if ($certificate->final_score)
                            <td class="label">Nilai Akhir</td>
                            <td class="value">{{ $certificate->final_score }}%</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="label">Tanggal Terbit</td>
                        <td class="value">{{ $certificate->issue_date->translatedFormat('d F Y') }}</td>
                        @if (isset($certificate->metadata['total_hours']) && $certificate->metadata['total_hours'] > 0)
                            <td class="label">Durasi</td>
                            <td class="value">{{ $certificate->metadata['total_hours'] }} Jam</td>
                        @endif
                    </tr>
                </table>
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

        <div class="cert-number">No: {{ $certificate->certificate_number }} | Verifikasi:
            {{ $certificate->verification_url }}</div>
    </div>
</body>

</html>
