<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\RoleController;

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
/*---ROLE---*/

Route::get('/role/select', [RoleController::class, 'selectRoles']);

/*---BUILDING---*/
Route::get('/building/find/{id}', [BuildingController::class, 'findBuilding']);
Route::get('/building/select', [BuildingController::class, 'selectBuildings']);
Route::post('/building/store', [BuildingController::class, 'storeBuilding']);
Route::put('/building/update/{id}', [BuildingController::class, 'updateBuilding']);
Route::delete('/building/delete/{id}', [BuildingController::class, 'deleteBuilding']);

/*---LEVEL---*/
Route::get('/level/find/{id}', [LevelController::class, 'findLevel']);
Route::get('/level/select', [LevelController::class, 'selectLevels']);
Route::post('/level/store', [LevelController::class, 'storeLevel']);
Route::put('/level/update/{id}', [LevelController::class, 'updateLevel']);
Route::delete('/level/delete/{id}', [LevelController::class, 'deleteLevel']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
