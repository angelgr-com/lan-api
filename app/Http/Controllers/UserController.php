<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        // If request is validated, encrypt password and generate token
        try {
            $validatedData['password']=bcrypt($validatedData['password']);
            $user = User::create($validatedData);

            // $token = $user->createToken('token')->accessToken;
            Log::info('User registered successfully. Username: '.$user->username);

            return response()->json([
                'message' => 'User registered successfully',
                // 'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $exception) {
            Log::info('Register failed. Error: '.$exception->getMessage());
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

    public function login(Request $request)
    {
        // Validate request data
        $credentials = $request->validate([
            'email' => 'required|string|email|min:8|max:64',
            'password' => 'required|string|min:8|max:32|'
        ]);

        // Attempt to login user with provided credentials
        if (auth()->attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            return response([
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
                'token' => $user->createToken('authToken')->accessToken
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid user or password.'
            ], 401);
        }
    }

    public function logout (Request $request) {
        try {
            $token = $request->user()->token();
            $token->revoke();
            return response()->json([
                'message' => 'Logout successful.'
            ], 200);
        } catch (Exception $exception) {
            Log::info('Logout failed. Error: '.$exception->getMessage());
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
