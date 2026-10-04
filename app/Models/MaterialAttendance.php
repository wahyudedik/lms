<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $material_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $attendance_date
 * @property string $status
 * @property string|null $alasan
 * @property int|null $recorded_by
 * @property-read \App\Models\Material|null $material
 * @property-read \App\Models\User|null $student
 * @property-read \App\Models\User|null $recorder
 * @property-read string $status_display
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance byStudent($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance forCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance forDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance forDateRange($from, $to)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance forMaterial($materialId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaterialAttendance query()
 *
 * @mixin \Eloquent
 */
class MaterialAttendance extends Model
{
    public const STATUS_HADIR = 'hadir';

    public const STATUS_SAKIT = 'sakit';

    public const STATUS_IJIN = 'ijin';

    public const STATUS_KELUAR = 'keluar';

    /**
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_HADIR,
        self::STATUS_SAKIT,
        self::STATUS_IJIN,
        self::STATUS_KELUAR,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'material_id',
        'user_id',
        'attendance_date',
        'status',
        'alasan',
        'recorded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attendance_date' => 'date:Y-m-d',
            'material_id' => 'integer',
            'user_id' => 'integer',
            'recorded_by' => 'integer',
        ];
    }

    /**
     * Relationships
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scopes
     */
    public function scopeForMaterial(Builder $query, $materialId): Builder
    {
        return $query->where('material_attendances.material_id', $materialId);
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('material_attendances.attendance_date', $date);
    }

    public function scopeForDateRange(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('material_attendances.attendance_date', [$from, $to]);
    }

    public function scopeByStudent(Builder $query, $userId): Builder
    {
        return $query->where('material_attendances.user_id', $userId);
    }

    public function scopeForCourse(Builder $query, $courseId): Builder
    {
        return $query->join('materials', 'materials.id', '=', 'material_attendances.material_id')
            ->where('materials.course_id', $courseId)
            ->select('material_attendances.*');
    }

    /**
     * Attributes
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_IJIN => 'Ijin',
            self::STATUS_KELUAR => 'Keluar',
            default => 'Hadir',
        };
    }

    /**
     * Bulk upsert absensi per (material, user, tanggal).
     *
     * @param  array<int, array{user_id: int|string, status: string, alasan?: string|null}>  $rows
     * @return array{created: int, updated: int}
     */
    public static function upsertBulk(int $materialId, string $date, array $rows, ?int $recordedBy): array
    {
        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            $attributes = [
                'material_id' => $materialId,
                'user_id' => (int) $row['user_id'],
                'attendance_date' => $date,
            ];

            $existing = static::where($attributes)->first();

            if ($existing) {
                $existing->update([
                    'status' => $row['status'],
                    'alasan' => $row['alasan'] ?? null,
                    'recorded_by' => $recordedBy,
                ]);
                $updated++;
            } else {
                static::create([
                    ...$attributes,
                    'status' => $row['status'],
                    'alasan' => $row['alasan'] ?? null,
                    'recorded_by' => $recordedBy,
                ]);
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated];
    }
}
