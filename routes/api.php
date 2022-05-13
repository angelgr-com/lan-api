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
    function () {
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
    function () {
        Route::get('/texts/', [TextController::class, 'getAll']);
        Route::get('/texts/id/{id}', [TextController::class, 'getTextById']);
        Route::get('/texts/cefr/{cefr}', [TextController::class, 'textsByCefr']);
        Route::get('/texts/en-es/{textId}', [TextController::class, 'esText']);
        Route::get('/texts/author/{id}', [TextController::class, 'authorFullName']);
        Route::get('/texts/languages', [TextController::class, 'languagesList']);
        Route::get('/texts/countries', [TextController::class, 'countriesList']);
        Route::get('/texts/zenquotes', [TextController::class, 'zenquotes']);
        Route::post('/texts/translation', [TextController::class, 'saveTranslation']);
    }
);
