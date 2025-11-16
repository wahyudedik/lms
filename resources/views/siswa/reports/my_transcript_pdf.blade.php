<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Grade Transcript - :name', ['name' => $user->name]) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #7c3aed;
        }

        .header h1 {
            font-size: 20px;
            color: #6d28d9;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 11px;
            color: #6b7280;
        }

        .student-info {
            background: #f9fafb;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #7c3aed;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #374151;
        }

        .info-value {
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #7c3aed;
            color: white;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border: 1px solid #d1d5db;
        }

        th {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .summary-box {
            background: #faf5ff;
            border: 2px solid #c4b5fd;
            padding: 20px;
            margin-top: 30px;
            border-radius: 5px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #6d28d9;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-grid {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .summary-item {
            flex: 1;
        }

        .summary-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #5b21b6;
            margin-top: 5px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .no-exams {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>TRANSKRIP NILAI SISWA</h1>
        <h2>{{ $course->title }}</h2>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-row">
            <div class="info-label">Nama Siswa</div>
            <div class="info-value">{{ $user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $user->email }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kursus</div>
            <div class="info-value">{{ $course->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Instruktur</div>
            <div class="info-value">{{ $course->instructor->name }}</div>
        </div>
    </div>

    @if ($exams->count() > 0)
        <!-- Exams Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th>{{ __('Exam Name') }}</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">{{ __('Pass Score') }}</th>
                    <th class="text-center">Skor (%)</th>
                    <th class="text-center">Poin</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalScore = 0;
                    $completedExams = 0;
                    $passedExams = 0;
                @endphp

                @foreach ($exams as $index => $exam)
                    @php
                        $attempt = $exam->attempts->first();
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $exam->title }}</td>
                        <td class="text-center">{{ $exam->duration_minutes }} mnt</td>
                        <td class="text-center">{{ $exam->pass_score }}%</td>
                        <td class="text-center">
                            @if ($attempt)
                                <strong>{{ number_format($attempt->score, 2) }}%</strong>
                                @php
                                    $totalScore += $attempt->score;
                                    $completedExams++;
                                    if ($attempt->passed) {
                                        $passedExams++;
                                    }
                                @endphp
                            @else
                                <span class="badge badge-warning">Belum Dikerjakan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($attempt)
                                {{ number_format($attempt->total_points_earned, 2) }} /
                                {{ number_format($attempt->total_points_possible, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($attempt)
                                <span class="badge {{ $attempt->passed ? 'badge-success' : 'badge-danger' }}">
                                    {{ $attempt->passed ? 'LULUS' : 'TIDAK LULUS' }}
                                </span>
                            @else
                                <span class="badge badge-warning">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($attempt && $attempt->submitted_at)
                                {{ $attempt->submitted_at->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-title">RINGKASAN NILAI</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">{{ __('Total Exams') }}</div>
                    <div class="summary-value">{{ $exams->count() }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Selesai</div>
                    <div class="summary-value">{{ $completedExams }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Lulus</div>
                    <div class="summary-value">{{ $passedExams }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">{{ __('Average Score') }}</div>
                    <div class="summary-value">
                        {{ $completedExams > 0 ? number_format($totalScore / $completedExams, 2) : '0.00' }}%
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="no-exams">
            <p>{{ __('No exams in this course yet.') }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem LMS.</p>
        <p>© {{ date('Y') }} Laravel LMS - All rights reserved.</p>
    </div>
</body>

</html>
