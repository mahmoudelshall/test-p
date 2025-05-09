<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create roles
         $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );

        $guest = Role::firstOrCreate(
            ['name' => 'guest'],
            ['guard_name' => 'web']
        );

        // Assign all permissions to super-admin
        $permissions = Permission::all();
        $superAdmin->syncPermissions($permissions);
    }
}
