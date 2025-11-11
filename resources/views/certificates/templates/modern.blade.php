<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            padding: 30px;
            width: 100%;
            height: 100vh;
        }

        .certificate-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            height: 100%;
        }

        .header-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15px;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
            border-radius: 20px 20px 0 0;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
            padding-top: 20px;
        }

        .logo-placeholder {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .certificate-title {
            font-size: 52px;
            font-weight: 900;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 5px;
        }

        .certificate-subtitle {
            font-size: 18px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .certificate-body {
            text-align: center;
            margin: 40px 0;
        }

        .certificate-text {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 20px;
        }

        .student-name {
            font-size: 44px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a 0%, #7e22ce 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 25px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .course-title {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin: 20px 0;
        }

        .achievement-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 30px auto;
            max-width: 600px;
            border: 3px solid #3b82f6;
        }

        .achievement-row {
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }

        .achievement-item {
            text-align: center;
        }

        .achievement-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .achievement-value {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .grade-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .grade-text {
            font-size: 56px;
            font-weight: 900;
            color: white;
        }

        .certificate-footer {
            display: table;
            width: 100%;
            margin-top: 50px;
        }

        .signature-box {
            display: table-cell;
            text-align: center;
            padding: 0 30px;
            vertical-align: bottom;
        }

        .signature-line {
            width: 200px;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
            margin: 50px auto 10px;
            border-radius: 2px;
        }

        .signature-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
            margin-bottom: 3px;
        }

        .signature-title {
            color: #6b7280;
            font-size: 13px;
        }

        .certificate-footer-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .certificate-number {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1px;
        }

        .verification-url {
            font-size: 10px;
            color: #3b82f6;
            margin-top: 5px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(59, 130, 246, 0.03);
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="header-bar"></div>
        <div class="watermark">CERTIFIED</div>

        <div class="certificate-header">
            <div class="logo-placeholder">🎓</div>
            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of Excellence</div>
        </div>

        <div class="certificate-body">
            <p class="certificate-text">This is to proudly certify that</p>

            <div class="student-name">{{ $certificate->student_name }}</div>

            <p class="certificate-text">has successfully completed</p>

            <div class="course-title">{{ $certificate->course_title }}</div>

            @if ($certificate->grade)
                <div class="grade-circle">
                    <div class="grade-text">{{ $certificate->grade }}</div>
                </div>
            @endif

            <div class="achievement-box">
                <div class="achievement-row">
                    <div class="achievement-item">
                        <div class="achievement-label">Completion Date</div>
                        <div class="achievement-value">{{ $certificate->completion_date->format('M d, Y') }}</div>
                    </div>
                    @if ($certificate->final_score)
                        <div class="achievement-item">
                            <div class="achievement-label">Final Score</div>
                            <div class="achievement-value">{{ $certificate->final_score }}%</div>
                        </div>
                    @endif
                </div>
                @if (isset($certificate->metadata['total_hours']))
                    <div class="achievement-row">
                        <div class="achievement-item">
                            <div class="achievement-label">Course Duration</div>
                            <div class="achievement-value">{{ $certificate->metadata['total_hours'] }} Hours</div>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-label">Issue Date</div>
                            <div class="achievement-value">{{ $certificate->issue_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="certificate-footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $certificate->instructor_name ?? 'Instructor' }}</div>
                <div class="signature-title">Course Instructor</div>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Academic Director</div>
                <div class="signature-title">LMS Platform</div>
            </div>
        </div>

        <div class="certificate-footer-info">
            <div class="certificate-number">
                Certificate Number: {{ $certificate->certificate_number }}
            </div>
            <div class="verification-url">
                Verify at: {{ $certificate->verification_url }}
            </div>
        </div>
    </div>
</body>

</html>
