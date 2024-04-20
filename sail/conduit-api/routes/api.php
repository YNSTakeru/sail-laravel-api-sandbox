<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
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

Route::post("users/login", [AuthController::class, "login"]);
Route::post("users", [AuthController::class, "register"]);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource("articles", ArticleController::class)->only(["index", "show"]);


Route::middleware('auth:api')->group(function () {
    Route::apiResource("articles", ArticleController::class)->except(["index", "show"]);
    Route::put("articles/{slug}/favorite", [ArticleController::class, "updateFavorite"]);
});
