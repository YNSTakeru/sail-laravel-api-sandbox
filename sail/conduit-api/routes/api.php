<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
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

Route::get('articles/page', [ArticleController::class, 'page']);
Route::middleware('auth:api')->get('articles/feed', [ArticleController::class, 'feed']);

Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);


Route::apiResource('profiles', ProfileController::class)->only(['show']);

Route::apiResource('tags', TagController::class)->only(['index']);

Route::get('tags/popular', [TagController::class, 'popular']);


Route::prefix('articles')->group(function () {
    Route::get('/{slug}/comments', [CommentController::class, 'index']);
});



Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->controller(UserController::class)->group(function () {
        Route::get('', 'show');
        Route::put('', 'update');
    });


    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);


    Route::prefix('profiles')->controller(ProfileController::class)->group(function () {
        Route::post('/{id}/follow', 'follow');
        Route::delete('/{id}/follow', 'unfollow');
    });

    Route::prefix('articles')->group(function () {
        Route::controller(ArticleController::class)->group(function () {
            Route::post('/{slug}/favorite', 'favorite');
            Route::delete('/{slug}/favorite', 'unfavorite');
        });

        Route::controller(CommentController::class)->group(function () {
            Route::post('/{slug}/comments', 'store');
            Route::delete('/{slug}/comments/{id}', 'destroy');
        });
    });
});
