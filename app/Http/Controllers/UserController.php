<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\CompleteUserProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Native;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

            $token = $user->createToken('token')->accessToken;
            Log::info('User registered successfully. Username: '.$user->username);

            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $exception) {
            Log::error('Register failed. Error: '.$exception->getMessage());
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

    public function profile() {
        Log::info('
            Showing the user profile for user: '
            .auth()->user()->id
            .PHP_EOL
            .auth()->user()->username
        );

        return response()->json(['user' => auth()->user()], 200);
    }

    public function completeUserProfile(Request $request) {
        try {
            $country_id = DB::table('countries')
                        ->where('name', '=', $request->country)
                        ->value('id');
            $language_id = DB::table('languages')
                            ->where('name', '=', $request->native_language)
                            ->value('id');
            $studying_language_id = DB::table('languages')
            ->where('name', '=', $request->studying_language)
            ->value('id');

            // Update user
            $user = User::where('id', '=', auth('api')->user()->id)->first();
            $user->country_id = $country_id;
            $user->save();

            // Save new register in Natives table
            $native_language = new Native();
            $native_language->user_id = $user->id;
            $native_language->language_id = $language_id;
            $native_language->save();

            // Save new register in Natives table
            $studying_language = new Student();
            $studying_language->user_id = $user->id;
            $studying_language->language_id = $language_id;
            $studying_language->save();

            return response()->json([
                'message' => 'User profile completed successfully',
            ], 200);
        } catch (\Exception $exception) {
            Log::error('Complete user profile failed. Error: '.$exception->getMessage());
            return response()->json([
                'message' => 'Languages failed',
                'Error' => $exception->getMessage(),
                'Code' => $exception->getCode(),
                'File' => $exception->getFile(),
                'Line' => $exception->getLine(),
                'Trace' => $exception->getTrace(),
            ], 500);     
        }
    }

    public function editProfile(EditProfileRequest $request)
    {
        Log::info(
            'Editing the user profile for user: '
            .auth()->user()->id
            .PHP_EOL
            .auth()->user()->username
        );

        $isDataChanged = false;

        // Search for logged in user
        $user = User::where('id', '=', auth('api')->user()->id)->first();

        // Reject changes if an aunthenticated user tries to edit another user
        if($user->id != auth()->user()->id) {
            Log::info('Unauthorized user profile edit for user: '.auth()->user()->id);
            return response()->json([
                'message' => 'Action unauthorized',
            ], 400);
        }

        // Change data if request is different from user's data: 
        if($user->first_name != $request->first_name) {
            Log::info(
                'Changed first_name for user: '.
                auth()->user()->id
                .PHP_EOL
                .'before: '.$user->first_name
                .', after: '.$request->first_name
            );
            $user->first_name = $request->first_name;
            $isDataChanged = true;
        }
        if($user->last_name != $request->last_name) {
            Log::info(
                'Changed last_name for user: '.
                auth()->user()->id.PHP_EOL.
                'before: '.$user->last_name.
                ', after: '.$request->last_name
            );
            $user->last_name = $request->last_name;
            $isDataChanged = true;
        }
        if($user->username != $request->username) {
            Log::info(
                'Changed username for user: '.
                auth()->user()->id.PHP_EOL
                .'before: '.$user->username
                .', after: '.$request->username
            );
            $user->username = $request->username;
            $isDataChanged = true;
        }
        if($user->email != $request->email) {
            Log::info(
                'Changed email for user: '.
                auth()->user()->id.PHP_EOL
                .'before: '.$user->email
                .', after: '.$request->email
            );
            $user->email = $request->email;
            $isDataChanged = true;
        }
        
        // Save data is there have been any change
        if($isDataChanged) {
            Log::info('Profile changed for user: '.
                auth()->user()->id
                .PHP_EOL
                .auth()->user()->username
            );
            $user->save();

            return response()->json([
                'message' => 'User has been edited successfully',
                'user' => User::where('email', $request->email)->first(),
            ], 200);
        } else {
            Log::info('Profile has not changed for user: '.
                auth()->user()->id
                .PHP_EOL
                .auth()->user()->username
            );

            return response()->json([
                'message' => 'User has not been edited',
                'user' => auth()->user(),
            ], 400);
        }
    }

    public function deleteProfile () {
        // Search for logged in user
        $user = User::where('id', '=', auth('api')->user()->id)->first();

        // Reject if an aunthenticated user tries to delete another user
        if($user->id != auth()->user()->id) {
            Log::info('Unauthorized user profile edit for user: '.auth()->user()->id);
            return response()->json([
                'message' => 'Action unauthorized',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Your user has been deleted successfully.'
        ], 200);
    }
}
