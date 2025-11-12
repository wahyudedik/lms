<?php

namespace App\Constants;

class AuthorizationMessages
{
    // Course access messages
    public const COURSE_ACCESS_DENIED = 'Anda tidak memiliki akses ke kelas ini.';
    public const COURSE_NOT_FOUND = 'Kelas tidak ditemukan.';

    // Exam access messages
    public const EXAM_ACCESS_DENIED = 'Anda tidak memiliki akses ke ujian ini.';
    public const EXAM_NOT_AVAILABLE = 'Ujian tidak tersedia saat ini.';
    public const EXAM_ENROLLMENT_REQUIRED = 'Anda harus terdaftar di kelas ini untuk mengikuti ujian.';

    // Material access messages
    public const MATERIAL_ACCESS_DENIED = 'Anda tidak memiliki akses ke materi ini.';

    // Question access messages
    public const QUESTION_ACCESS_DENIED = 'Anda tidak memiliki akses ke soal ini.';

    // Enrollment access messages
    public const ENROLLMENT_ACCESS_DENIED = 'Anda tidak memiliki akses ke pendaftaran ini.';

    // Certificate access messages
    public const CERTIFICATE_ACCESS_DENIED = 'Anda tidak memiliki izin untuk mengakses sertifikat ini.';
    public const CERTIFICATE_GENERATE_DENIED = 'Anda tidak memiliki izin untuk membuat sertifikat untuk pendaftaran ini.';

    // Forum access messages
    public const FORUM_ACCESS_DENIED = 'Anda tidak memiliki izin untuk melakukan aksi ini.';
    public const FORUM_THREAD_LOCKED = 'Thread ini terkunci!';

    // General messages
    public const UNAUTHORIZED_ACTION = 'Aksi tidak diizinkan.';
    public const UNAUTHORIZED = 'Tidak diizinkan.';
    public const ACCESS_DENIED = 'Akses ditolak.';

    // Attempt access messages
    public const ATTEMPT_ACCESS_DENIED = 'Anda tidak memiliki akses ke attempt ini.';
    public const ATTEMPT_NOT_OWNER = 'Attempt ini bukan milik Anda.';
    public const ATTEMPT_INVALID = 'Attempt tidak valid.';
    public const RESULTS_NOT_AVAILABLE = 'Hasil belum tersedia.';
}

