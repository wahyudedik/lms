<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Certificate Template
    |--------------------------------------------------------------------------
    |
    | Choose from available templates:
    | - 'default'    : Classic design with decorative elements
    | - 'modern'     : Modern gradient design with colorful elements
    | - 'elegant'    : Elegant gold-themed formal certificate
    | - 'minimalist' : Clean minimalist design with bold typography
    |
    */

    'template' => env('CERTIFICATE_TEMPLATE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Institution Information
    |--------------------------------------------------------------------------
    |
    | These details will be displayed on the certificate
    |
    */

    'institution' => [
        'name' => env('CERTIFICATE_INSTITUTION_NAME', 'LMS Platform'),
        'logo' => env('CERTIFICATE_LOGO_PATH', null), // Path to logo file
        'director' => env('CERTIFICATE_DIRECTOR_NAME', 'Director of Education'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade Configuration
    |--------------------------------------------------------------------------
    |
    | Define grade thresholds and labels
    |
    */

    'grades' => [
        'A' => ['min' => 90, 'label' => 'Excellent'],
        'B' => ['min' => 80, 'label' => 'Very Good'],
        'C' => ['min' => 70, 'label' => 'Good'],
        'D' => ['min' => 60, 'label' => 'Satisfactory'],
        'F' => ['min' => 0, 'label' => 'Needs Improvement'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificate Number Format
    |--------------------------------------------------------------------------
    |
    | Format for certificate numbers
    | Available variables: {YEAR}, {MONTH}, {RANDOM}
    |
    */

    'number_format' => 'CERT-{YEAR}-{RANDOM}',
    'random_length' => 8,

    /*
    |--------------------------------------------------------------------------
    | PDF Configuration
    |--------------------------------------------------------------------------
    |
    | PDF generation settings
    |
    */

    'pdf' => [
        'paper' => 'a4',
        'orientation' => 'landscape',
        'dpi' => 150,
    ],

    /*
    |--------------------------------------------------------------------------
    | Colors Configuration
    |--------------------------------------------------------------------------
    |
    | Customize certificate colors for different templates
    |
    */

    'colors' => [
        'primary' => env('CERTIFICATE_PRIMARY_COLOR', '#3b82f6'),
        'secondary' => env('CERTIFICATE_SECONDARY_COLOR', '#8b5cf6'),
        'accent' => env('CERTIFICATE_ACCENT_COLOR', '#ec4899'),
        'text' => env('CERTIFICATE_TEXT_COLOR', '#1e293b'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Generate Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatic certificate generation
    |
    */

    'auto_generate' => [
        'enabled' => env('CERTIFICATE_AUTO_GENERATE', true),
        'on_completion' => true,
        'notify_student' => env('CERTIFICATE_NOTIFY_STUDENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    |
    | Public verification settings
    |
    */

    'verification' => [
        'enabled' => true,
        'public_url' => env('APP_URL') . '/verify-certificate',
        'qr_code_enabled' => env('CERTIFICATE_QR_CODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    |
    | Where to store generated PDFs
    |
    */

    'storage' => [
        'disk' => env('CERTIFICATE_STORAGE_DISK', 'public'),
        'path' => 'certificates',
        'auto_save' => env('CERTIFICATE_AUTO_SAVE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | Email certificate to students
    |
    */

    'email' => [
        'enabled' => env('CERTIFICATE_EMAIL_ENABLED', false),
        'subject' => 'Congratulations! Your Certificate is Ready',
        'attach_pdf' => true,
    ],

];
