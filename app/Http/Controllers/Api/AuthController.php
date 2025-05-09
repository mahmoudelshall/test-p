<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\CountryCodeResource;
use App\Http\Resources\UserLoginResource;
use App\Jobs\ResetPasswordCodeJob;
use App\Jobs\SendVerificationCodeJob;
use App\Models\CountryCode;
use App\Models\Otp;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registrationForm()
    {
        $countryCodes = CountryCodeResource::collection(CountryCode::all());
        $data = [
            'countryCodes' => $countryCodes,
        ];
        return message(false, $data, []);
    }

    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        $password = $request->input('password');
        $user = null;

        $user = User::where('email', $request->email)->first();


        // Validate user and password
        if (!$user || !Hash::check($password, $user->password)) {
            return message(true, null, [__('Invalid credentials')], 401);
        }

        if (!$user->verified) {
            return message(true, null, [__('Account not verified. Please verifiy your account.')], 403);
        }

        // Create token and return data
        $token = JWTAuth::fromUser($user);

        return message(false, [
            'token' => $token,
            'user' => new UserLoginResource($user),
        ], [], 200);
    }

    public function logout()
    {
        auth()->logout();
        return message(false, null, [__('Logged out successfully')], 200);
    }

    public function register(RegistrationRequest $request)
    {
        try {
            $data = $request->validated();

            $otp = Otp::where('email', $request->email)->where('code', $request->code)->first();

            if (!$otp) {
                return message(true, null, [__('Invalid code or email.')], 400);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'verified' => true,
            ]);

            // Assign role with ID 2
            $role = Role::find(2);
            if ($role) {
                $user->assignRole($role->name);
            }

            // OTP is valid, now delete it
            $otp->delete();

            return message(false, null, [__('User registered successfully.')], 200);
        } catch (Exception $e) {
            Log::error("User registration: {$e->getMessage()}");
            return message(true, null, [__('Unable to register in the User.')], 500);
        }
    }


    // public function createUser(CreateUserRequest $request)
    // {
    //     try {
    //         $data = $request->validated();
    //         //   $data['name']['ar'] = $data['name']['ar'] ?? $data['name']['en'];
    //         $name = $data['name'];
    //         $data['name'] = [];
    //         $data['name']['en'] =  $name;

    //         $user = User::create([
    //             'name' => $data['name'],
    //             'email' => $data['email'],
    //             'password' => Hash::make(rand(10000000, 99999999)),
    //             'verified' => false,
    //         ]);

    //         $user->mobile()->create([
    //             'mobile' => $data['mobile'],
    //             'country_code_id' => $data['country_code_id'],
    //         ]);
    //         // Assign role with ID 1
    //         $role = Role::find(2);
    //         if ($role) {
    //             $user->assignRole($role->name);
    //         }

    //         return message(false, null, [__('User created successfully.')], 200);
    //     } catch (Exception $e) {
    //         Log::error("User registration: {$e->getMessage()}");
    //         return message(true, null, [__('Unable to log in the User after registration.')], 500);
    //     }
    // }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        try {
            SendVerificationCodeJob::dispatch($request->name, $request->email);
            return message(false, null, [__('OTP code sent successfully.')], 200);
        } catch (Exception $e) {
            Log::error("Send OTP: Unable to send OTP due to error: {$e->getMessage()}");
            return message(true, null, [__('Unable to send OTP')], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        try {
            $otp = Otp::where('email', $request->email)->where('code', $request->code)->first();

            if (!$otp) {
                return message(true, null, [__('Invalid code or email.')], 400);
            }

            // // Optional: check if expired (assuming 5-minute expiration)
            // if ($otp->updated_at->lt(now()->subMinutes(5))) {
            //     return message(true, null, [__('OTP has expired.')], 400);

            // }

            return message(false, null, [__('OTP code verified successfully.')], 200);
        } catch (Exception $e) {
            Log::error("Verify OTP: Unable to verify OTP due to error: {$e->getMessage()}");
            return message(true, null, [__('Unable to Verify OTP')], 500);
        }
    }

    // public function verifyAccount(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'nullable|email|exists:users,email',
    //         'mobile' => 'nullable|string',
    //         'country_code_id' => 'required_with:mobile|exists:country_codes,id',
    //         'verification_code' => 'required|digits:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return message(true, null, $validator->errors(), 422);
    //     }

    //     $user = null;

    //     if ($request->filled('email')) {
    //         $user = User::where('email', $request->email)->first();
    //     } elseif ($request->filled('mobile') && $request->filled('country_code_id')) {
    //         $mobile = Mobile::where([
    //             'mobile' => $request->mobile,
    //             'country_code_id' => $request->country_code_id,
    //             'model_type' => User::class,
    //         ])->first();

    //         $user = $mobile?->model;
    //     }

    //     if (!$user) {
    //         return message(true, null, [__('User not found.')], 404);
    //     }

    //     if ($user->verification_code !== $request->verification_code) {
    //         return message(true, null, [__('Invalid verification code.')], 422);
    //     }

    //     $user->verified = true;
    //     $user->verification_code = null;
    //     $user->save();

    //     return message(false, null, [__('Account verified successfully.')], 200);
    // }

    public function sendResetPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        // Find user by email or mobile
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return message(true, null, [__('User not found')], 404);
        }

        // Dispatch job (reuse existing one)
        ResetPasswordCodeJob::dispatch($user);

        return message(false, null, [__('Reset Password code sent successfully.')], 200);
    }

    public function verifyResetPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return message(true, null, [__('User not found.')], 404);
        }

        $otp = Otp::where('email', $request->email)->where('code', $request->code)->first();

        if (!$otp) {
            return message(true, null, [__('Invalid code.')], 400);
        }

        return message(false, null, [__('Valid reset password code.')], 200);
    }

    /**
     *  store new Password.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:6',
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[a-z]/',      // at least one lowercase letter
                'regex:/[A-Z]/',      // at least one uppercase letter
                'regex:/\d/',         // at least one digit
                'regex:/[@$!%*?&]/',  // at least one special character
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return message(true, null, [__('User not found.')], 404);
        }

        try {
            // Check if the provided verification code matches
            $otp = Otp::where('email', $request->email)->where('code', $request->code)->first();

            if (!$otp) {
                return message(true, null, [__('Invalid code.')], 400);
            }

            // Update the password with the provided one
            $user->password = Hash::make($request->password);
            $user->save();

            // OTP is valid, now delete it
            $otp->delete();
            return message(false, null, [__('Password updated successfully')], 200);
        } catch (Exception $e) {
            Log::error("Reset Password: Unable to reset password due to error: {$e->getMessage()}");
            return message(true, null, [__('Unable to reset password')], 500);
        }
    }


    public function getCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return message(true, null, $validator->errors(), 422);
        }

        $user = null;

        if ($request->filled('email')) {
            $otp = Otp::where('email', $request->email)->first();
        }

        if (!$otp) {
            return message(true, null, [__('OTP not found.')], 404);
        }
        return message(false, ['code' => $otp->code], [__('data retrieved successfully')], 200);
    }
}
