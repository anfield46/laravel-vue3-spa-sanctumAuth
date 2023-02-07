<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogOutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', [LoginController::class, "index"]);

Route::post("register", [RegisterController::class, "store"]);
Route::post("login", [LoginController::class, "login"]);
Route::post("logout", [LogOutController::class, "logout"]);

Route::apiResource("book", BookController::class);

// Route::get('/user', [\App\Http\Controllers\UserController::class, 'index']);
Route::post('/getuser', [\App\Http\Controllers\UserController::class, 'getuser']);
Route::get('/user/create', [\App\Http\Controllers\UserController::class, 'create']);
Route::post('/user/store', [\App\Http\Controllers\UserController::class, 'store']);
Route::get('/user/edit/{id}', [\App\Http\Controllers\UserController::class, 'edit']);
Route::put('/user/update/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::delete('/user/delete/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

Route::get('/unitkerja', [\App\Http\Controllers\UnitKerjaController::class, 'index']);
Route::post('/getunitkerja', [\App\Http\Controllers\UnitKerjaController::class, 'getunitkerja']);
Route::get('/dataunitkerja', [\App\Http\Controllers\UnitKerjaController::class, 'dataunitkerja']);
