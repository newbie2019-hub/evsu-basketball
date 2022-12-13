<?php

use App\Http\Controllers\AssistantCoachController;
use App\Http\Controllers\AthleteCoachController;
use App\Http\Controllers\AthletesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DrillCategoryController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\GameDrillController;
use App\Http\Controllers\GameScheduleController;
use App\Http\Controllers\PerformanceEvaluationController;
use App\Http\Controllers\PlayerStatisticsController;
use App\Http\Controllers\TeamController;

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
        Route::post('update', 'update');
        Route::post('logout', 'logout');
        Route::get('authUser', 'authUser');
    });

    Route::get('dashboard', [DashboardController::class, 'getData']);
    Route::apiResource('performances', PerformanceEvaluationController::class);

    Route::get('evaluations/all', [EvaluationController::class, 'getEvaluations']);
    Route::apiResource('evaluations', EvaluationController::class);

    Route::get('getTeams', [TeamController::class, 'getTeams']);
    Route::apiResource('teams', TeamController::class);

    Route::put('athletes/assign/{athlete}', [AthletesController::class, 'assign']);
    Route::get('getAthletes', [AthletesController::class, 'getAthletes']);
    Route::apiResource('athletes', AthletesController::class);

    Route::get('getCoaches', [AssistantCoachController::class, 'getCoaches']);
    Route::apiResource('coaches/assign', AthleteCoachController::class);
    Route::apiResource('coaches', AssistantCoachController::class);

    Route::get('categories/all', [DrillCategoryController::class, 'all']);

    Route::get('schedules', [GameScheduleController::class, 'schedules']);
    Route::apiResource('gameschedule', GameScheduleController::class);

    Route::get('drills/getDrills', [GameDrillController::class, 'getDrills']);
    Route::apiResource('drills', GameDrillController::class);

    Route::apiResource('statistics', PlayerStatisticsController::class);

    Route::get('categories/all', [DrillCategoryController::class, 'all']);
    Route::apiResource('categories', DrillCategoryController::class);
});
