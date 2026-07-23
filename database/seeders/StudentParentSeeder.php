<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentParentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $students = DB::table('students')->get();

        $data = [];

        foreach ($students as $student) {
            $data[] = [
                'student_id' => $student->id,
                'parent_name' => 'Orang Tua '.$student->full_name,
                'relationship' => ['father', 'mother'][rand(0, 1)],
                'email' => null,
                'phone_number' => '08'.rand(1000000000, 9999999999),
                'occupation' => 'Wiraswasta',
                'address' => 'Alamat orang tua',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('student_parents')->insert($data);
    }
}
