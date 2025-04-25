<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::get();

        return response()->json(['data' => $roles]);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        // Hide the pivot field from each permission
        $role->permissions->each(function ($permission) {
            $permission->makeHidden('pivot');
        });

        return response()->json(['data' => $role]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Role created', 'data' => $role]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);

        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['message' => 'Role updated', 'data' => $role]);
    }
}
