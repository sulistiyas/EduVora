<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'school_id' => 1,
                'school_name' => 'SMA Negeri 1 Jakarta',
                'npsn' => '20100101',
                'nss' => '301010101001',
                'school_type' => 'Senior High',
                'accreditation' => 'A',
                'kkm_default' => 75,
                'address' => 'Jl. Sudirman No. 1',
                'city' => 'Jakarta',
                'district' => 'Tanah Abang',
                'province' => 'DKI Jakarta',
                'postal_code' => '10210',
                'contact_phone' => '02112345678',
                'contact_email' => 'info@sman1jkt.sch.id',
                'website' => 'https://sman1jkt.sch.id',
                'headmaster_name' => 'Budi Santoso',
                'headmaster_nip' => '196501011990011001',
                'logo' => null,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'school_id' => 2,
                'school_name' => 'SMP Negeri 2 Bandung',
                'npsn' => '20200202',
                'nss' => '302020202002',
                'school_type' => 'Junior High',
                'accreditation' => 'A',
                'kkm_default' => 70,
                'address' => 'Jl. Asia Afrika No. 45',
                'city' => 'Bandung',
                'district' => 'Lengkong',
                'province' => 'Jawa Barat',
                'postal_code' => '40261',
                'contact_phone' => '02287654321',
                'contact_email' => 'info@smpn2bdg.sch.id',
                'website' => 'https://smpn2bdg.sch.id',
                'headmaster_name' => 'Siti Aminah',
                'headmaster_nip' => '197002021995022002',
                'logo' => null,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'school_id' => 3,
                'school_name' => 'SMK Negeri 3 Surabaya',
                'npsn' => '20300303',
                'nss' => '303030303003',
                'school_type' => 'Senior High',
                'accreditation' => 'B',
                'kkm_default' => 72,
                'address' => 'Jl. Raya Darmo No. 100',
                'city' => 'Surabaya',
                'district' => 'Wonokromo',
                'province' => 'Jawa Timur',
                'postal_code' => '60241',
                'contact_phone' => '03111223344',
                'contact_email' => 'info@smkn3sby.sch.id',
                'website' => 'https://smkn3sby.sch.id',
                'headmaster_name' => 'Ahmad Hidayat',
                'headmaster_nip' => '196812121993031003',
                'logo' => null,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'school_id' => 4,
                'school_name' => 'SD Negeri 4 Yogyakarta',
                'npsn' => '20400404',
                'nss' => '304040404004',
                'school_type' => 'Elementary',
                'accreditation' => 'A',
                'kkm_default' => 68,
                'address' => 'Jl. Malioboro No. 10',
                'city' => 'Yogyakarta',
                'district' => 'Gondomanan',
                'province' => 'DI Yogyakarta',
                'postal_code' => '55122',
                'contact_phone' => '0274556677',
                'contact_email' => 'info@sdn4jogja.sch.id',
                'website' => 'https://sdn4jogja.sch.id',
                'headmaster_name' => 'Rina Kusuma',
                'headmaster_nip' => '197511111998021004',
                'logo' => null,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('school_profiles')->insert($data);
    }
}
