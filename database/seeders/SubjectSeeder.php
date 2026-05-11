<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        $subjects = [
            [
                'subject_name' => 'Matematika',
                'subject_code' => 'MTK',
                'category' => 'Umum',
            ],
            [
                'subject_name' => 'Bahasa Indonesia',
                'subject_code' => 'BIN',
                'category' => 'Umum',
            ],
            [
                'subject_name' => 'Bahasa Inggris',
                'subject_code' => 'BIG',
                'category' => 'Umum',
            ],
            [
                'subject_name' => 'IPA',
                'subject_code' => 'IPA',
                'category' => 'Sains',
            ],
            [
                'subject_name' => 'IPS',
                'subject_code' => 'IPS',
                'category' => 'Sosial',
            ],
            [
                'subject_name' => 'PJOK',
                'subject_code' => 'PJK',
                'category' => 'Olahraga',
            ],
            [
                'subject_name' => 'Produktif TKJ',
                'subject_code' => 'TKJ',
                'category' => 'Kejuruan',
            ],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'school_id' => $schoolId,
                'subject_name' => $subject['subject_name'],
                'subject_code' => $subject['subject_code'],
                'category' => $subject['category'],
                'credits' => 2,
                'hours_per_week' => 4,
                'description' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}