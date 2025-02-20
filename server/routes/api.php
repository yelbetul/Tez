<?php

use App\Http\Controllers\FatalWorkAccidentsBySectorController;
use App\Http\Controllers\TemporaryDisabilityDaysBySectorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkAccidentsBySectorController;

//Work Accidents By Sector
Route::get('/work-accidents-by-sector',                                         [WorkAccidentsBySectorController::class, 'index']);
Route::get('/work-accidents-by-sector/year/{year}',                             [WorkAccidentsBySectorController::class, 'indexByYear']);
Route::get('/work-accidents-by-sector/group/{groupId}',                         [WorkAccidentsBySectorController::class, 'indexByGroupId']);
Route::get('/work-accidents-by-sector/sector-code/{sectorCode}',                [WorkAccidentsBySectorController::class, 'indexBySectorCode']);
Route::get('/work-accidents-by-sector/group-code/{groupCode}',                  [WorkAccidentsBySectorController::class, 'indexByGroupCode']);
Route::get('/work-accidents-by-sector/sub-group-code/{subGroupCode}',           [WorkAccidentsBySectorController::class, 'indexBySubGroupCode']);
Route::get('/work-accidents-by-sector/pure-code/{pureCode}',                    [WorkAccidentsBySectorController::class, 'indexByPureCode']);
Route::post('/work-accidents-by-sector/store',                                  [WorkAccidentsBySectorController::class, 'store']);
Route::put('/work-accidents-by-sector/update/{id}',                             [WorkAccidentsBySectorController::class, 'update']);
Route::delete('/work-accidents-by-sector/delete/{id}',                          [WorkAccidentsBySectorController::class, 'destroy']);

// Fatal Work Accidents By Sector
Route::get('/fatal-work-accidents-by-sector',                                   [FatalWorkAccidentsBySectorController::class, 'index']);
Route::get('/fatal-work-accidents-by-sector/year/{year}',                       [FatalWorkAccidentsBySectorController::class, 'indexByYear']);
Route::get('/fatal-work-accidents-by-sector/group/{groupId}',                   [FatalWorkAccidentsBySectorController::class, 'indexByGroupId']);
Route::get('/fatal-work-accidents-by-sector/sector-code/{sectorCode}',          [FatalWorkAccidentsBySectorController::class, 'indexBySectorCode']);
Route::get('/fatal-work-accidents-by-sector/group-code/{groupCode}',            [FatalWorkAccidentsBySectorController::class, 'indexByGroupCode']);
Route::get('/fatal-work-accidents-by-sector/sub-group-code/{subGroupCode}',     [FatalWorkAccidentsBySectorController::class, 'indexBySubGroupCode']);
Route::get('/fatal-work-accidents-by-sector/pure-code/{pureCode}',              [FatalWorkAccidentsBySectorController::class, 'indexByPureCode']);
Route::post('/fatal-work-accidents-by-sector/store',                            [FatalWorkAccidentsBySectorController::class, 'store']);
Route::put('/fatal-work-accidents-by-sector/update/{id}',                       [FatalWorkAccidentsBySectorController::class, 'update']);
Route::delete('/fatal-work-accidents-by-sector/delete/{id}',                    [FatalWorkAccidentsBySectorController::class, 'destroy']);

// Temporary Disability Day By Sector
Route::get('/temporary-disability-day-by-sector',                               [TemporaryDisabilityDaysBySectorController::class, 'index']);
Route::get('/temporary-disability-day-by-sector/year/{year}',                   [TemporaryDisabilityDaysBySectorController::class, 'indexByYear']);
Route::get('/temporary-disability-day-by-sector/group/{groupId}',               [TemporaryDisabilityDaysBySectorController::class, 'indexByGroupId']);
Route::get('/temporary-disability-day-by-sector/sector/{sectorCode}',           [TemporaryDisabilityDaysBySectorController::class, 'indexBySectorCode']);
Route::get('/temporary-disability-day-by-sector/group-code/{groupCode}',        [TemporaryDisabilityDaysBySectorController::class, 'indexByGroupCode']);
Route::get('/temporary-disability-day-by-sector/sub-group-code/{subGroupCode}', [TemporaryDisabilityDaysBySectorController::class, 'indexBySubGroupCode']);
Route::get('/temporary-disability-day-by-sector/pure-code/{pureCode}',          [TemporaryDisabilityDaysBySectorController::class, 'indexByPureCode']);
Route::post('/temporary-disability-day-by-sector/store',                        [TemporaryDisabilityDaysBySectorController::class, 'store']);
Route::put('/temporary-disability-day-by-sector/update/{id}',                   [TemporaryDisabilityDaysBySectorController::class, 'update']);
Route::delete('/temporary-disability-day-by-sector/delete/{id}',                [TemporaryDisabilityDaysBySectorController::class, 'destroy']);
