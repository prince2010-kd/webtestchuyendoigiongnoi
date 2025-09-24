<?php

use App\Http\Controllers\api\nhomquyen\NhomQuyenController;
use App\Http\Controllers\api\post\PostController;
use App\Http\Controllers\api\member\MemberController;
use App\Http\Controllers\api\user\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Route::post('/login', [UserController::class, 'login']);
// Route::post('/user/create',[MemberController::class, 'create']);

Route::post('/register', [MemberController::class, 'create']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'getDetail']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});