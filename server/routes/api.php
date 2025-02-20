<?php

use App\Http\Controllers\DisabilityDaysOccupationalDiseasesByProvinceController;
use App\Http\Controllers\FatalWorkAccidentsByProvinceController;
use App\Http\Controllers\FatalWorkAccidentsBySectorController;
use App\Http\Controllers\TemporaryDisabilityDaysByProvinceController;
use App\Http\Controllers\TemporaryDisabilityDaysBySectorController;
use App\Http\Controllers\WorkAccidentsByProvinceController;
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

// Work Accidents By Province

Route::get('/work-accidents-by-province',                                       [WorkAccidentsByProvinceController::class, 'index']);
Route::get('/work-accidents-by-province/year/{year}',                           [WorkAccidentsByProvinceController::class, 'indexByYear']);
Route::get('/work-accidents-by-province/province-id/{provinceId}',              [WorkAccidentsByProvinceController::class, 'indexByProvinceId']);
Route::get('/work-accidents-by-province/province-code/{provinceCode}',          [WorkAccidentsByProvinceController::class, 'indexByProvinceCode']);
Route::post('/work-accidents-by-province/store',                                [WorkAccidentsByProvinceController::class, 'store']);
Route::put('/work-accidents-by-province/update/{id}',                           [WorkAccidentsByProvinceController::class, 'update']);
Route::delete('/work-accidents-by-province/delete/{id}',                        [WorkAccidentsByProvinceController::class, 'destroy']);

// Fatal Work Accidents By Province

Route::get('/fatal-work-accidents-by-province',                                  [FatalWorkAccidentsByProvinceController::class, 'index']);
Route::get('/fatal-work-accidents-by-province/year/{year}',                      [FatalWorkAccidentsByProvinceController::class, 'indexByYear']);
Route::get('/fatal-work-accidents-by-province/province-id/{provinceId}',         [FatalWorkAccidentsByProvinceController::class, 'indexByProvinceId']);
Route::get('/fatal-work-accidents-by-province/province-code/{provinceCode}',     [FatalWorkAccidentsByProvinceController::class, 'indexByProvinceCode']);
Route::post('/fatal-work-accidents-by-province/store',                           [FatalWorkAccidentsByProvinceController::class, 'store']);
Route::put('/fatal-work-accidents-by-province/update/{id}',                      [FatalWorkAccidentsByProvinceController::class, 'update']);
Route::delete('/fatal-work-accidents-by-province/delete/{id}',                   [FatalWorkAccidentsByProvinceController::class, 'destroy']);

// Temporary Disability Day By Province

Route::get('/temporary-disability-day-by-province',                                    [TemporaryDisabilityDaysByProvinceController::class, 'index']);
Route::get('/temporary-disability-day-by-province/year/{year}',                        [TemporaryDisabilityDaysByProvinceController::class, 'indexByYear']);
Route::get('/temporary-disability-day-by-province/province/{provinceId}',              [TemporaryDisabilityDaysByProvinceController::class, 'indexByProvinceId']);
Route::get('/temporary-disability-day-by-province/province-code/{provinceCode}',       [TemporaryDisabilityDaysByProvinceController::class, 'indexByProvinceCode']);
Route::post('/temporary-disability-day-by-province/store',                             [TemporaryDisabilityDaysByProvinceController::class, 'store']);
Route::put('/temporary-disability-day-by-province/update/{id}',                        [TemporaryDisabilityDaysByProvinceController::class, 'update']);
Route::delete('/temporary-disability-day-by-province/delete/{id}',                     [TemporaryDisabilityDaysByProvinceController::class, 'destroy']);

// Disability Days Occupational Diseases By Province

Route::get('/disability-days-occ-dis-by-province',                                  [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'index']);
Route::get('/disability-days-occ-dis-by-province/year/{year}',                      [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'indexByYear']);
Route::get('/disability-days-occ-dis-by-province/province/{provinceId}',            [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'indexByProvinceId']);
Route::get('/disability-days-occ-dis-by-province/province-code/{provinceCode}',     [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'indexByProvinceCode']);
Route::post('/disability-days-occ-dis-by-province/store',                           [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'store']);
Route::put('/disability-days-occ-dis-by-province/update/{id}',                      [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'update']);
Route::delete('/disability-days-occ-dis-by-province/delete/{id}',                   [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'destroy']);
