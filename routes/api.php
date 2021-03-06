<?php

use App\Http\Controllers\ApiAuthenticationController;
use App\Http\Controllers\ApiNewsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('posts', PostController::class)
    ->scoped([
        'post' => 'slug',
    ])
    ->missing(function () {
        return response()->json(['message' => 'Post not found'], 404);
    });

Route::apiResource('posts.comments', CommentController::class)
    ->scoped([
        'post' => 'slug',
        'comment' => 'id',
    ])
    ->missing(function () {
        return response()->json(['message' => 'Comment not found']);
    });

Route::apiResource('news', ApiNewsController::class)
    ->scoped([
        'news' => 'slug'
    ])
    ->missing(function () {
        return response()->json(['message' => 'News not found']);
    });

Route::post('registration', [ApiAuthenticationController::class, 'registration']);

Route::post('login', [ApiAuthenticationController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('profile', [ApiAuthenticationController::class, 'profile']);
    Route::post('logout', [ApiAuthenticationController::class, 'logout']);
});


