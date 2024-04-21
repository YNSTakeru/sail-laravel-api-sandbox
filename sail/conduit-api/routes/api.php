<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('users/login', [AuthController::class, 'login']);
Route::post('users', [AuthController::class, 'register']);

Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);


Route::apiResource('profiles', ProfileController::class)->only(['show']);

Route::middleware('auth:api')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'show');
        Route::put('user', 'update');
    });
    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
    Route::put('articles/{slug}/favorite', [ArticleController::class, 'updateFavorite']);

    Route::controller(ProfileController::class)->group(function () {
        Route::post('profiles/{id}/follow', 'follow');
    });
});
