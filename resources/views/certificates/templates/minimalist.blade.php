<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            background: #ffffff;
            padding: 50px;
            width: 100%;
            height: 100vh;
        }

        .certificate-container {
            background: white;
            padding: 80px;
            border: 1px solid #000;
            position: relative;
            height: 100%;
        }

        .accent-line {
            position: absolute;
            left: 0;
            width: 8px;
            height: 100%;
            background: #000;
        }

        .certificate-header {
            text-align: left;
            margin-bottom: 80px;
            padding-left: 40px;
        }

        .certificate-title {
            font-size: 64px;
            font-weight: 900;
            color: #000;
            text-transform: uppercase;
            letter-spacing: -2px;
            margin-bottom: 5px;
        }

        .certificate-subtitle {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 300;
        }

        .certificate-body {
            padding-left: 40px;
            margin: 60px 0;
        }

        .label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .student-name {
            font-size: 52px;
            font-weight: 700;
            color: #000;
            margin-bottom: 40px;
            line-height: 1.1;
        }

        .course-title {
            font-size: 28px;
            font-weight: 600;
            color: #000;
            margin-bottom: 40px;
            line-height: 1.3;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-top: 60px;
        }

        .info-item {
            display: table-cell;
            padding-right: 60px;
        }

        .info-label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 600;
            color: #000;
        }

        .grade-box {
            display: inline-block;
            padding: 20px 30px;
            border: 3px solid #000;
            margin: 30px 0;
        }

        .grade-value {
            font-size: 48px;
            font-weight: 900;
            color: #000;
            line-height: 1;
        }

        .certificate-footer {
            position: absolute;
            bottom: 80px;
            left: 40px;
            right: 80px;
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid #000;
        }

        .signature-block {
            text-align: left;
        }

        .signature-name {
            font-weight: 700;
            color: #000;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .signature-title {
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-meta {
            position: absolute;
            bottom: 40px;
            right: 80px;
            text-align: right;
        }

        .certificate-number {
            font-size: 9px;
            color: #999;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="accent-line"></div>

        <div class="certificate-header">
            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of completion</div>
        </div>

        <div class="certificate-body">
            <div class="label">Awarded to</div>
            <div class="student-name">{{ $certificate->student_name }}</div>

            <div class="label">For completing</div>
            <div class="course-title">{{ $certificate->course_title }}</div>

            @if ($certificate->grade)
                <div class="label">Grade</div>
                <div class="grade-box">
                    <div class="grade-value">{{ $certificate->grade }}</div>
                </div>
            @endif

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Completed</div>
                    <div class="info-value">{{ $certificate->completion_date->format('M d, Y') }}</div>
                </div>
                @if ($certificate->final_score)
                    <div class="info-item">
                        <div class="info-label">Score</div>
                        <div class="info-value">{{ $certificate->final_score }}%</div>
                    </div>
                @endif
                @if (isset($certificate->metadata['total_hours']))
                    <div class="info-item">
                        <div class="info-label">Duration</div>
                        <div class="info-value">{{ $certificate->metadata['total_hours'] }} hrs</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="certificate-footer">
            <div class="signature-block">
                <div class="signature-name">{{ $certificate->instructor_name ?? 'Instructor' }}</div>
                <div class="signature-title">Course Instructor</div>
            </div>

            <div class="signature-block">
                <div class="signature-name">Academic Director</div>
                <div class="signature-title">Learning Platform</div>
            </div>
        </div>

        <div class="certificate-meta">
            <div class="certificate-number">{{ $certificate->certificate_number }}</div>
        </div>
    </div>
</body>

</html>
