<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountryCodesTableSeeder::class);
        $this->call(UserPermissionsSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AddAdminUserSeeder::class);

    }
}
