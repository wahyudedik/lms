<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Nilai - {{ $exam->title }}</title>
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
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 20px;
            color: #1e40af;
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

        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
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
            margin-top: 20px;
        }

        thead {
            background: #2563eb;
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

        tbody tr:hover {
            background: #f3f4f6;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .statistics {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background: #eff6ff;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN NILAI UJIAN</h1>
        <h2>{{ $exam->title }}</h2>
        <p>{{ $exam->course->title }} | Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Exam Information -->
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Kursus</div>
            <div class="info-value">{{ $exam->course->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Instruktur</div>
            <div class="info-value">{{ $exam->course->instructor->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Durasi Ujian</div>
            <div class="info-value">{{ $exam->duration_minutes }} Menit</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nilai Lulus</div>
            <div class="info-value">{{ $exam->pass_score }}%</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Soal</div>
            <div class="info-value">{{ $exam->questions->count() }} Soal</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Peserta</div>
            <div class="info-value">{{ $attempts->count() }} Siswa</div>
        </div>
    </div>

    <!-- Statistics -->
    @php
        $passedCount = $attempts->where('passed', true)->count();
        $failedCount = $attempts->where('passed', false)->count();
        $avgScore = $attempts->avg('score');
        $maxScore = $attempts->max('score');
        $minScore = $attempts->min('score');
    @endphp

    <div class="statistics">
        <div class="stat-item">
            <div class="stat-label">Rata-rata</div>
            <div class="stat-value">{{ number_format($avgScore, 1) }}%</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Tertinggi</div>
            <div class="stat-value">{{ number_format($maxScore, 1) }}%</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Terendah</div>
            <div class="stat-value">{{ number_format($minScore, 1) }}%</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Lulus</div>
            <div class="stat-value">{{ $passedCount }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Tidak Lulus</div>
            <div class="stat-value">{{ $failedCount }}</div>
        </div>
    </div>

    <!-- Grades Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Nama Siswa</th>
                <th>Email</th>
                <th class="text-center">Skor (%)</th>
                <th class="text-center">Poin</th>
                <th class="text-center">Waktu (detik)</th>
                <th class="text-center">Status</th>
                <th class="text-center">Pelanggaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attempts->sortByDesc('score') as $index => $attempt)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $attempt->is_guest ? ($attempt->guest_name ?? 'Tamu') : ($attempt->user->name ?? 'Tidak diketahui') }}</td>
                    <td>{{ $attempt->is_guest ? ($attempt->guest_email ?? '-') : ($attempt->user->email ?? '-') }}</td>
                    <td class="text-center">
                        @if (!is_null($attempt->score))
                            <strong>{{ number_format($attempt->score, 2) }}%</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if (!is_null($attempt->total_points_possible) && $attempt->total_points_possible > 0)
                            {{ number_format($attempt->total_points_earned, 2) }} /
                            {{ number_format($attempt->total_points_possible, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($attempt->time_spent)
                            {{ gmdate('H:i:s', $attempt->time_spent) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $attempt->passed ? 'badge-success' : 'badge-danger' }}">
                            {{ $attempt->passed ? 'LULUS' : 'TIDAK LULUS' }}
                        </span>
                    </td>
                    <td class="text-center">{{ count($attempt->violations ?? []) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem LMS.</p>
        <p>© {{ date('Y') }} Laravel LMS - All rights reserved.</p>
    </div>
</body>

</html>
