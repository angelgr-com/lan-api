<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // If request is validated in RegisterRequest, encrypt password and generate token
        try {
            $user = User::create([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'profile_picture' => $request->get('profile_picture'),
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->password),
                'is_admin' => false,
            ]);

            $token = $user->createToken('token')->accessToken;

            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
            ], 200);
        } catch (Exception $exception) {

            return response()->json([
                'message' => 'Register failed',
                'Error' => $exception->getMessage(),
                'Code' => $exception->getCode(),
                'File' => $exception->getFile(),
                'Line' => $exception->getLine(),
                'Trace' => $exception->getTrace(),
            ], 401);
        }
    }
}
