<?php

use App\Http\Controllers\AppealController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\NewsController;
use App\Http\Middleware\SuggestAppeal;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/news', [NewsController::class, 'getList'])->name("news_list");

Route::get('/news/{slug}', [NewsController::class, 'getDetails'])->name("news_item");

Route::match(['get', 'post'], '/appeal', AppealController::class)->name('appeal')
    ->withoutMiddleware([SuggestAppeal::class]);

Route::match(['get', 'post'], '/registration', [AuthenticationController::class, 'registration'])
    ->name('registration');

Route::match(['get', 'post'], '/login', [AuthenticationController::class, 'login'])
    ->name('login');

Route::get('/logout', [AuthenticationController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/profile', function () {
    return new UserResource(Auth::user());
})->name('profile')->middleware('auth');
