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
      Route::put('/users/profile', [UserController::class, 'editProfile']);
      // Route::post('/users/forget', [UserController::class, 'forget']);
      // Route::post('/users/reset', [UserController::class, 'reset']);
      Route::delete('/users', [UserController::class, 'deleteProfile']);
      // Route::delete('/users/{user_id}', [UserController::class, 'deleteById']);
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
      Route::get('/texts/all', [TextController::class, 'index']);
      Route::get('/texts/zenquotes', [TextController::class, 'create']);
      Route::get('/texts/', [TextController::class, 'show']);
      Route::get('/texts/all', [TextController::class, 'index']);
      Route::get('/texts/author/{id}', [TextController::class, 'authorName']);
  }
);