<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;

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
/*---BUILDING---*/
Route::get('/building/find/{id}', [BuildingController::class, 'findBuilding']);
Route::get('/building/select', [BuildingController::class, 'selectBuildings']);
Route::post('/building/store', [BuildingController::class, 'storeBuilding']);
Route::put('/building/update/{id}', [BuildingController::class, 'updateBuilding']);
Route::delete('/building/delete/{id}', [BuildingController::class, 'deleteBuilding']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
