<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthUserController;
use App\Http\Controllers\Api\V1\Chat\ChatController;
use App\Http\Middleware\HandleErrors;
use App\Http\Middleware\JwtMiddleware;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::middleware([HandleErrors::class])->group(function () {
    // Auth route
    Route::post('register', [AuthUserController::class, 'register'])->name('register');
    Route::post('login', [AuthUserController::class, 'login'])->name('login');
    Route::post('logout', [AuthUserController::class, 'logout'])->name('logout');
    Route::middleware([JwtMiddleware::class])->group(function () {
        // Chat route
        Route::get('get_user_list', [ChatController::class, 'get_user_list'])->name('getUserList');
        Route::post('send_message', [ChatController::class, 'send_message'])->name('sendMessage');
        Route::post('get_message', [ChatController::class, 'get_message'])->name('getMessage');
    });
});
