<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AddAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Yall@$Host22'),
                'verified' => true,

            ]
        );

        // Create or update mobile for the user
        $user->mobile()->updateOrCreate(
            [],
            [
                'mobile' => '01000000000',
                'country_code_id' => 63, // Adjust this to your actual country code ID
            ]
        );
        // Assign role with ID 1
        $role = Role::find(1);
        if ($role) {
            $user->assignRole($role->name);
        }
    }
}
