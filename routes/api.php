<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\CandidateController;
use App\Http\Controllers\API\v1\ElectionController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\VotingController;
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
        Route::resource('/users', UserController::class)->except(['create', 'edit']);
        Route::resource('/elections', ElectionController::class)->except(['create', 'edit']);
        Route::resource('/candidates', CandidateController::class)->except(['create', 'edit']);
        Route::resource('/votings', VotingController::class)->except(['create', 'edit']);
    });

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
