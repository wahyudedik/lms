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
            font-family: 'Georgia', 'Times New Roman', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            width: 100%;
            height: 100vh;
        }

        .certificate-container {
            background: white;
            padding: 60px;
            border: 20px solid #f8f9fa;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            position: relative;
            height: 100%;
        }

        .certificate-border {
            border: 3px solid #667eea;
            padding: 40px;
            height: 100%;
            position: relative;
        }

        .decorative-corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #764ba2;
        }

        .corner-tl {
            top: -3px;
            left: -3px;
            border-right: none;
            border-bottom: none;
        }

        .corner-tr {
            top: -3px;
            right: -3px;
            border-left: none;
            border-bottom: none;
        }

        .corner-bl {
            bottom: -3px;
            left: -3px;
            border-right: none;
            border-top: none;
        }

        .corner-br {
            bottom: -3px;
            right: -3px;
            border-left: none;
            border-top: none;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 8px;
            margin-bottom: 10px;
        }

        .certificate-subtitle {
            font-size: 20px;
            color: #6c757d;
            font-style: italic;
        }

        .certificate-body {
            text-align: center;
            margin: 50px 0;
        }

        .certificate-text {
            font-size: 18px;
            color: #495057;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .student-name {
            font-size: 42px;
            font-weight: bold;
            color: #212529;
            margin: 30px 0;
            text-transform: uppercase;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            display: inline-block;
        }

        .course-title {
            font-size: 28px;
            font-weight: bold;
            color: #764ba2;
            margin: 20px 0;
        }

        .course-description {
            font-size: 16px;
            color: #6c757d;
            margin: 20px auto;
            max-width: 600px;
            line-height: 1.6;
        }

        .certificate-details {
            display: table;
            margin: 40px auto;
            text-align: left;
        }

        .detail-row {
            display: table-row;
        }

        .detail-label {
            display: table-cell;
            padding: 8px 20px 8px 0;
            font-weight: bold;
            color: #495057;
        }

        .detail-value {
            display: table-cell;
            padding: 8px 0;
            color: #212529;
        }

        .certificate-footer {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-section {
            display: table-cell;
            text-align: center;
            padding: 0 40px;
        }

        .signature-line {
            border-top: 2px solid #212529;
            width: 200px;
            margin: 60px auto 10px;
        }

        .signature-name {
            font-weight: bold;
            color: #212529;
            font-size: 16px;
        }

        .signature-title {
            color: #6c757d;
            font-size: 14px;
            font-style: italic;
        }

        .certificate-number {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #6c757d;
        }

        .verification-section {
            position: absolute;
            bottom: 20px;
            right: 40px;
            text-align: right;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 2px solid #dee2e6;
            display: inline-block;
            padding: 5px;
        }

        .verify-text {
            font-size: 10px;
            color: #6c757d;
            margin-top: 5px;
        }

        .grade-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .medal {
            font-size: 48px;
            color: #ffc107;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="decorative-corner corner-tl"></div>
            <div class="decorative-corner corner-tr"></div>
            <div class="decorative-corner corner-bl"></div>
            <div class="decorative-corner corner-br"></div>

            <div class="certificate-header">
                <div class="medal">🏆</div>
                <div class="certificate-title">Certificate</div>
                <div class="certificate-subtitle">of Achievement</div>
            </div>

            <div class="certificate-body">
                <p class="certificate-text">
                    This is to certify that
                </p>

                <div class="student-name">{{ $certificate->student_name }}</div>

                <p class="certificate-text">
                    has successfully completed the course
                </p>

                <div class="course-title">{{ $certificate->course_title }}</div>

                @if ($certificate->course_description)
                    <div class="course-description">
                        {{ Str::limit($certificate->course_description, 150) }}
                    </div>
                @endif

                @if ($certificate->grade)
                    <div class="grade-badge">Grade: {{ $certificate->grade }}</div>
                @endif

                <div class="certificate-details">
                    <div class="detail-row">
                        <div class="detail-label">Completion Date:</div>
                        <div class="detail-value">{{ $certificate->completion_date->format('F d, Y') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Issue Date:</div>
                        <div class="detail-value">{{ $certificate->issue_date->format('F d, Y') }}</div>
                    </div>
                    @if ($certificate->final_score)
                        <div class="detail-row">
                            <div class="detail-label">Final Score:</div>
                            <div class="detail-value">{{ $certificate->final_score }}%</div>
                        </div>
                    @endif
                    @if (isset($certificate->metadata['total_hours']))
                        <div class="detail-row">
                            <div class="detail-label">Duration:</div>
                            <div class="detail-value">{{ $certificate->metadata['total_hours'] }} hours</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="certificate-footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->instructor_name ?? 'Instructor' }}</div>
                    <div class="signature-title">Course Instructor</div>
                </div>

                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">Director of Education</div>
                    <div class="signature-title">LMS Platform</div>
                </div>
            </div>

            <div class="certificate-number">
                Certificate No: {{ $certificate->certificate_number }}
            </div>

            <div class="verification-section">
                <div class="verify-text">Scan to verify</div>
                <div class="qr-code">
                    <!-- QR Code placeholder -->
                    <div style="text-align: center; padding-top: 25px; font-size: 10px;">
                        QR<br>CODE
                    </div>
                </div>
                <div class="verify-text">{{ $certificate->verification_url }}</div>
            </div>
        </div>
    </div>
</body>

</html>
