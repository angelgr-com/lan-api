<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TextController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User endpoints
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
        Route::post('/users', [UserController::class, 'deleteProfile']);
        Route::post('/users/profile/add-data', [UserController::class, 'completeUserProfile']);
        Route::get('/users/profile/complete', [UserController::class, 'isProfileComplete']);
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
        Route::get('/texts/en-es/{textId}', [TextController::class, 'retrieveCorrectTranslation']);
        Route::post('/texts/translation', [TextController::class, 'saveUserTranslation']);
        Route::get('/texts/author/{id}', [TextController::class, 'authorFullName']);
        Route::get('/texts/languages', [TextController::class, 'languagesList']);
        Route::get('/texts/countries', [TextController::class, 'countriesList']);
        Route::get('/texts/zenquotes', [TextController::class, 'zenquotes']);
    }
);
