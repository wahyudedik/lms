<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'education_level',
        'capacity',
        'is_general',
    ];

    protected $casts = [
        'is_general' => 'boolean',
        'capacity' => 'integer',
    ];

    public const EDUCATION_LEVELS = [
        'sd' => 'SD',
        'smp' => 'SMP',
        'sma' => 'SMA',
        'smk' => 'SMK',
        'kuliah' => 'Kuliah',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'school_class_id');
    }

    public function getEducationLevelLabelAttribute(): string
    {
        if ($this->is_general) {
            return __('General');
        }

        if (!$this->education_level) {
            return '-';
        }

        return self::EDUCATION_LEVELS[$this->education_level] ?? $this->education_level;
    }

    public static function general(): self
    {
        return self::query()->firstOrCreate(
            ['is_general' => true],
            [
                'name' => 'Kelas Umum',
                'education_level' => null,
                'capacity' => 999999,
            ]
        );
    }
}

