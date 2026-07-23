<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1;

        $rooms = [
            [
                'room_name' => 'Ruang A1',
                'code' => 'A1',
                'type' => 'classroom',
            ],
            [
                'room_name' => 'Ruang A2',
                'code' => 'A2',
                'type' => 'classroom',
            ],
            [
                'room_name' => 'Lab Komputer 1',
                'code' => 'LAB-KOM-1',
                'type' => 'lab',
            ],
            [
                'room_name' => 'Lab Jaringan',
                'code' => 'LAB-JAR',
                'type' => 'lab',
            ],
            [
                'room_name' => 'Aula Sekolah',
                'code' => 'AULA',
                'type' => 'sport',
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert([
                'school_id' => $schoolId,
                'room_name' => $room['room_name'],
                'code' => $room['code'],
                'type' => $room['type'],
                'floor' => 1,
                'building' => 'Gedung Utama',
                'capacity' => 36,
                'facility' => json_encode([
                    'AC',
                    'Projector',
                    'WiFi',
                ]),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
