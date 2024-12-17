<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Models\User;
use App\Http\Resources\AuthResource\UserResource;

class AuthUserController extends Controller
{
    // User registration
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                ]);

            $token = JWTAuth::fromUser($user);
            $user['token'] = $token;

            // send response to front end
            return successResponse( UserResource::make($user), __('api.Success Register'), 200);
        } catch (\Exception $exception) {
            // send response to front end
            return errorResponse(null, __('api.Internal Server Error'), 500);
        }
    }

    // User login
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('phone_number', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                // send response to front end
                return errorResponse(null, __('api.Invalid credentials'), 401);
            }

            // Get the authenticated user.
            $user = auth()->user();
            $token = JWTAuth::fromUser($user);
            $user['token'] = $token;

            // send response to front end
            return successResponse( UserResource::make($user), __('api.success_login'), 200);

        } catch (JWTException $e) {
            // send response to front end
            return errorResponse($e, __('api.Could not create token'), 500);
        }
    }

    // User logout
    public function logout()
    {
        try {
            // Remove Token
            JWTAuth::invalidate(JWTAuth::getToken());

            // send response to front end
            return successResponse( null, __('api.Successfully logged out'), 200);
        } catch (JWTException $e) {
            // send response to front end
            return errorResponse(null, __('api.Invalid Token'), 500);
        }
    }
}
