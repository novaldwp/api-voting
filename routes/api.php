<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\ElectionController;
use App\Http\Controllers\API\v1\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function() {
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/user/{user_id}', [UserController::class, 'show']);
        Route::put('/user/{user_id}', [UserController::class, 'update']);
        Route::delete('/user/{user_id}', [UserController::class, 'destroy']);

        Route::get('/elections', [ElectionController::class, 'index']);
        Route::post('/elections', [ElectionController::class, 'store']);
        Route::get('/election/{election_id}', [ElectionController::class, 'show']);
        Route::put('/election/{election_id}', [ElectionController::class, 'update']);
        Route::delete('/election/{election_id}', [ElectionController::class, 'destroy']);
    });

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
