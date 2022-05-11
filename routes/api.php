<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TextController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);
Route::group(
  [
    'middleware' => 'auth:api'
  ],
  function() {
      Route::post('/users/logout', [UserController::class, 'logout']);
      Route::get('/users/profile', [UserController::class, 'profile']);
      Route::put('/users/profile/edit', [UserController::class, 'editProfile']);
      Route::post('/users/forget', [UserController::class, 'forget']);
      Route::post('/users/reset', [UserController::class, 'reset']);
      Route::delete('/users/delete/', [UserController::class, 'deleteProfile']);
      Route::delete('/users/delete/{user_id}', [UserController::class, 'deleteById']);
  }
);

// Text endpoints
Route::group(
  [
      'middleware' => 'auth:api'
  ],
  function() {
      Route::get('/texts/languages', [TextController::class, 'languagesList']);
      Route::get('/texts/countries', [TextController::class, 'countriesList']);
  }
);