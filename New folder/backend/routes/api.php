<?php

use App\Http\Controllers\AthletesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameDrillController;
use App\Http\Controllers\GameScheduleController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::get('authUser', 'authUser');
    });

    Route::apiResource('athletes', AthletesController::class);
    Route::apiResource('gameschedule', GameScheduleController::class);
    Route::apiResource('drills', GameDrillController::class);
});
