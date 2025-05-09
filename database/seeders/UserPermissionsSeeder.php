<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'list-all-users',
                'display' => [
                    'en' => 'List All Users',
                    'ar' => 'عرض كل المستخدمين',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'show-user',
                'display' => [
                    'en' => 'Show User',
                    'ar' => 'عرض المستخدم',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'create-user',
                'display' => [
                    'en' => 'Create User',
                    'ar' => 'إنشاء مستخدم',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'edit-user',
                'display' => [
                    'en' => 'Edit User',
                    'ar' => 'تعديل المستخدم',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'delete-user',
                'display' => [
                    'en' => 'Delete User',
                    'ar' => 'حذف المستخدم',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'list-all-permissions',
                'display' => [
                    'en' => 'List All Permissions',
                    'ar' => 'عرض كل الأذونات',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'list-all-roles',
                'display' => [
                    'en' => 'List All Roles',
                    'ar' => 'عرض كل الأدوار',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'show-role',
                'display' => [
                    'en' => 'Show Role',
                    'ar' => 'عرض الدور',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'create-role',
                'display' => [
                    'en' => 'Create Role',
                    'ar' => 'إنشاء دور',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'edit-role',
                'display' => [
                    'en' => 'Edit Role',
                    'ar' => 'تعديل الدور',
                ],
                'permission_group' => 'User&Roles',
            ],
            [
                'name' => 'delete-role',
                'display' => [
                    'en' => 'Delete Role',
                    'ar' => 'حذف الدور',
                ],
                'permission_group' => 'User&Roles',
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'guard_name' => 'web',
                    'display' => $perm['display'],
                    'permission_group' => $perm['permission_group'],
                ]
            );
        }
    }
}
