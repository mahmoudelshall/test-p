<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            $permissions = PermissionResource::collection(Permission::all());

            return message(false, $permissions, [], 200);
        } catch (Exception $e) {
            Log::error("Index Permissions: Unable to retrieve permissions due to error: {$e->getMessage()}");
            return message(true, $e->getMessage(), [__('Unable to retrieve permissions')], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     try {
    //         // Validate parameters
    //         $validator = Validator::make($request->all(), [
    //             'name' => 'required|string|unique:permissions,name',
    //         ]);

    //         if ($validator->fails()) {
    //             return  message(true, null, $validator->errors());
    //         }

    //         $permission = Permission::create(['name' => $request->name]);

    //         return message(false, $permission, 'Permission created');
    //     } catch (Exception $e) {
    //         Log::error("Create Permission : system can not Create Permission for this error {$e->getMessage()}");
    //         return message(true, null, [__('System can not Create Permission')]);
    //     }
    // }
}
