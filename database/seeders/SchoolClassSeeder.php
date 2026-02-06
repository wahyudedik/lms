<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $generalClass = SchoolClass::general();

        DB::transaction(function () use ($generalClass) {
            User::query()
                ->where('role', 'siswa')
                ->whereNull('school_class_id')
                ->update(['school_class_id' => $generalClass->id]);
        });

        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        $defaults = [
            ['name' => 'Kelas 1', 'education_level' => 'sd', 'capacity' => 30],
            ['name' => 'Kelas 2', 'education_level' => 'sd', 'capacity' => 30],
            ['name' => 'Kelas 7', 'education_level' => 'smp', 'capacity' => 32],
            ['name' => 'Kelas 10', 'education_level' => 'sma', 'capacity' => 36],
            ['name' => 'Kelas 10', 'education_level' => 'smk', 'capacity' => 36],
            ['name' => 'Semester 1', 'education_level' => 'kuliah', 'capacity' => 40],
        ];

        DB::transaction(function () use ($defaults) {
            foreach ($defaults as $data) {
                SchoolClass::query()->firstOrCreate(
                    ['name' => $data['name'], 'education_level' => $data['education_level'], 'is_general' => false],
                    ['capacity' => $data['capacity']]
                );
            }
        });
    }
}

