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

    
}
