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
            font-family: 'Garamond', 'Georgia', serif;
            background: #1a1a1a;
            padding: 40px;
            width: 100%;
            height: 100vh;
        }

        .certificate-container {
            background: linear-gradient(135deg, #f5f5f0 0%, #ffffff 100%);
            padding: 60px;
            border: 25px solid #2c1810;
            box-shadow: 0 0 0 5px #d4af37, 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            height: 100%;
        }

        .inner-border {
            border: 3px double #d4af37;
            padding: 50px;
            height: 100%;
            position: relative;
        }

        .ornament {
            position: absolute;
            font-size: 48px;
            color: #d4af37;
            opacity: 0.6;
        }

        .ornament-tl {
            top: 20px;
            left: 20px;
        }

        .ornament-tr {
            top: 20px;
            right: 20px;
        }

        .ornament-bl {
            bottom: 20px;
            left: 20px;
        }

        .ornament-br {
            bottom: 20px;
            right: 20px;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .seal {
            width: 90px;
            height: 90px;
            border: 5px solid #d4af37;
            border-radius: 50%;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, #2c1810 0%, #1a1a1a 100%);
            position: relative;
        }

        .seal:before {
            content: '';
            position: absolute;
            width: 70px;
            height: 70px;
            border: 2px solid #d4af37;
            border-radius: 50%;
        }

        .seal-icon {
            font-size: 42px;
            color: #d4af37;
        }

        .certificate-title {
            font-size: 56px;
            font-weight: normal;
            color: #2c1810;
            text-transform: uppercase;
            letter-spacing: 12px;
            margin-bottom: 8px;
            font-style: italic;
        }

        .certificate-subtitle {
            font-size: 22px;
            color: #6b5d52;
            letter-spacing: 6px;
            font-weight: 300;
        }

        .divider {
            width: 150px;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #d4af37 50%, transparent 100%);
            margin: 25px auto;
        }

        .certificate-body {
            text-align: center;
            margin: 50px 0;
        }

        .certificate-text {
            font-size: 18px;
            color: #4a4a4a;
            margin-bottom: 25px;
            line-height: 1.8;
            font-style: italic;
        }

        .student-name {
            font-size: 48px;
            font-weight: normal;
            color: #2c1810;
            margin: 35px 0;
            text-transform: capitalize;
            border-bottom: 3px double #d4af37;
            padding-bottom: 15px;
            display: inline-block;
            letter-spacing: 3px;
        }

        .course-title {
            font-size: 32px;
            font-weight: 600;
            color: #2c1810;
            margin: 25px 0;
            letter-spacing: 2px;
        }

        .details-box {
            background: rgba(212, 175, 55, 0.08);
            border-left: 4px solid #d4af37;
            border-right: 4px solid #d4af37;
            padding: 25px;
            margin: 35px auto;
            max-width: 500px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            font-size: 15px;
        }

        .detail-label {
            color: #6b5d52;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
        }

        .detail-value {
            color: #2c1810;
            font-weight: 600;
        }

        .grade-badge {
            display: inline-block;
            width: 80px;
            height: 80px;
            border: 4px solid #d4af37;
            border-radius: 50%;
            line-height: 72px;
            font-size: 36px;
            font-weight: 700;
            color: #2c1810;
            margin: 20px 0;
            background: white;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .certificate-footer {
            display: table;
            width: 100%;
            margin-top: 60px;
        }

        .signature-section {
            display: table-cell;
            text-align: center;
            padding: 0 40px;
        }

        .signature-line {
            border-top: 2px solid #2c1810;
            width: 180px;
            margin: 50px auto 12px;
        }

        .signature-name {
            font-weight: 600;
            color: #2c1810;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .signature-title {
            color: #6b5d52;
            font-size: 13px;
            font-style: italic;
        }

        .certificate-number {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #9a8a7a;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="inner-border">
            <div class="ornament ornament-tl">❖</div>
            <div class="ornament ornament-tr">❖</div>
            <div class="ornament ornament-bl">❖</div>
            <div class="ornament ornament-br">❖</div>

            <div class="certificate-header">
                <div class="seal">
                    <div class="seal-icon">🎓</div>
                </div>
                <div class="certificate-title">Certificate</div>
                <div class="certificate-subtitle">Of Achievement</div>
                <div class="divider"></div>
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

                @if ($certificate->grade)
                    <div class="grade-badge">{{ $certificate->grade }}</div>
                @endif

                <div class="details-box">
                    <div class="detail-row">
                        <span class="detail-label">Completed On:</span>
                        <span class="detail-value">{{ $certificate->completion_date->format('F d, Y') }}</span>
                    </div>
                    @if ($certificate->final_score)
                        <div class="detail-row">
                            <span class="detail-label">Final Score:</span>
                            <span class="detail-value">{{ $certificate->final_score }}%</span>
                        </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Issued On:</span>
                        <span class="detail-value">{{ $certificate->issue_date->format('F d, Y') }}</span>
                    </div>
                    @if (isset($certificate->metadata['total_hours']))
                        <div class="detail-row">
                            <span class="detail-label">Duration:</span>
                            <span class="detail-value">{{ $certificate->metadata['total_hours'] }} Hours</span>
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
                    <div class="signature-name">Academic Director</div>
                    <div class="signature-title">Educational Institution</div>
                </div>
            </div>

            <div class="certificate-number">
                {{ $certificate->certificate_number }}
            </div>
        </div>
    </div>
</body>

</html>
