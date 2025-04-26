<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::paginate(10);
            $transformedUsers = UserResource::collection($users);
            // Append pagination metadata to the transformed data
            $pagination = [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'total_page' => $users->lastPage(),
            ];
            return message(false,  ['users' => $transformedUsers, 'pagination' => $pagination], [__('users Retrieved Successfully')]);
        } catch (Exception $e) {
            Log::error("Index Users: Unable to retrieve blogs due to error: {$e->getMessage()}");
            return message(true, $e->getMessage(), [__('Unable to retrieve Users')]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate parameters
            $validator = Validator::make($request->all(), [
                'name' => 'required|array',
                'name.en' => 'required|string',
                'name.ar' => 'nullable|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'avatar' => 'nullable|image|max:2048',
                'language' => 'required|in:en,ar',
                'gender' => 'required|in:male,female', 
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return message(true, null, $validator->errors());
            }
            $data = $request->all();
            // If 'ar' is missing, fallback to 'en'
            $data['name']['ar'] = $data['name']['ar'] ?? $data['name']['en'];

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'language' => $data['language'],
                'password' => bcrypt($data['password']),
                'gender' => $data['gender'],

            ]);

            // Assign Role
            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role->name);

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }

            return message(false, new UserResource ($user), 'User created');

        } catch (\Exception $e) {
            Log::error("Create User : system can not create user for this error {$e->getMessage()}");
            return message(true, null, [__('System can not create user')]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Find the Blog or fail if not found
            $user = User::where('id', $id)->firstOrFail();

            return  message(false, new UserResource($user), [__(' User Retrieved Successfully')]);
        } catch (Exception $e) {
            Log::error("Show User: Unable to retrieve User due to error: {$e->getMessage()}");
            return message(true, null, [__('User not found')]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validate parameters
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|array',
                'name.en' => 'sometimes|string',
                'name.ar' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:6',
                'avatar' => 'nullable|image|max:2048',
                'language' => 'sometimes|in:en,ar',
                'gender' => 'sometimes|in:male,female',
                'role_id' => 'sometimes|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return message(true, null, $validator->errors());
            }
            $data = $request->all();

            if (isset($data['name'])) {
                $user->setTranslations('name', $data['name']);
            }

            if (isset($data['email'])) {
                $user->email = $data['email'];
            }

            if (isset($data['language'])) {
                $user->language = $data['language'];
            }

            if (isset($data['password'])) {
                $user->password = bcrypt($data['password']);
            }

            if (isset($data['gender'])) {
                $user->gender = $data['gender'];
            }

            $user->save();

            if (isset($data['role_id'])) {
                 // Assign Role
                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role->name]);
            }

            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatar');
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }

            return message(false, new UserResource ($user), 'User updated');

        } catch (Exception $e) {
            Log::error("Update Role: system cannot update role for this error: {$e->getMessage()}");
            return message(true, null, [__('System cannot update role')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            Log::error("Delete User: system cannot delete user for this error: {$e->getMessage()}");
            return message(true, null, [__('System cannot delete user')]);
        }
    }
}
