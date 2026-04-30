<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('TRUNCATE roles,users,user_has_roles RESTART IDENTITY CASCADE');
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
