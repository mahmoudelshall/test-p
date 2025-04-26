<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::get();
            return message(false, $roles, []);
        } catch (Exception $e) {
            Log::error("Index roles: Unable to retrieve roles due to error: {$e->getMessage()}");
            return message(true, null, [__('Unable to retrieve roles')]);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            // Hide the pivot field from each permission
            $role->permissions->each(function ($permission) {
                $permission->makeHidden('pivot');
            });

            return message(false, $role, []);
        } catch (Exception $e) {
            Log::error("Show Role: Unable to retrieve role due to error: {$e->getMessage()}");
            return message(true, null, [__('role not found')]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate parameters
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            if ($validator->fails()) {
                return message(true, null, $validator->errors());
            }


            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return message(false, $role, 'Role created');
        } catch (\Exception $e) {
            Log::error("Create Role : system can not create role for this error {$e->getMessage()}");
            return message(true, null, [__('System can not create role')]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate parameters
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|unique:roles,name,' . $id,
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            if ($validator->fails()) {
                return message(true, null, $validator->errors());
            }

            $role = Role::findOrFail($id);

            if ($request->has('name')) {
                $role->name = $request->name;
                $role->save();
            }

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return message(false, $role, 'Role updated');
        } catch (Exception $e) {
            Log::error("Update Role: system cannot update role for this error: {$e->getMessage()}");
            return message(true, null, [__('System cannot update role')]);
        }
    }
}
