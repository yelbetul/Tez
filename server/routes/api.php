<?php

use App\Http\Controllers\AccidentsAndFatalitiesByInjuryController;
use App\Http\Controllers\AccidentsAndFatalitiesByOccupationController;
use App\Http\Controllers\AccidentsAndFatalitiesByInjuryLocationController;
use App\Http\Controllers\AccidentsAndFatalitiesByInjuryCauseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgeCodeController;
use App\Http\Controllers\DeviationController;
use App\Http\Controllers\DiagnosisGroupController;
use App\Http\Controllers\DisabilityDaysOccupationalDiseasesByProvinceController;
use App\Http\Controllers\EmployeeEmploymentDurationController;
use App\Http\Controllers\EmployeeGroupController;
use App\Http\Controllers\FatalWorkAccidentsByAgeController;
use App\Http\Controllers\FatalWorkAccidentsByMonthController;
use App\Http\Controllers\FatalWorkAccidentsByProvinceController;
use App\Http\Controllers\FatalWorkAccidentsBySectorController;
use App\Http\Controllers\GeneralActivityController;
use App\Http\Controllers\InjuryCauseController;
use App\Http\Controllers\InjuryLocationController;
use App\Http\Controllers\InjuryTypeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\OccDiseaseFatalitiesByOccupationController;
use App\Http\Controllers\OccupationalDiseaseByDiagnosisController;
use App\Http\Controllers\OccupationGroupController;
use App\Http\Controllers\ProvinceCodeController;
use App\Http\Controllers\SectorCodeController;
use App\Http\Controllers\SpecialActivitiesBeforeAccidentController;
use App\Http\Controllers\TemporaryDisabilityDaysByMonthController;
use App\Http\Controllers\TemporaryDisabilityDaysByProvinceController;
use App\Http\Controllers\TemporaryDisabilityDaysBySectorController;
use App\Http\Controllers\TimeIntervalController;
use App\Http\Controllers\WorkAccidentsByAgeController;
use App\Http\Controllers\WorkAccidentsByMonthController;
use App\Http\Controllers\WorkAccidentsByProvinceController;
use App\Http\Controllers\WorkAccidentsBySectorController;
use App\Http\Controllers\WorkEnvironmentController;
use App\Http\Controllers\WorkstationTypeController;
use App\Http\Controllers\AdminController;
 

// Admins

Route::get('/admin',             [AdminController::class, 'index']);
Route::post('/admin/store',      [AdminController::class, 'store']);
Route::get('/admin/{id}',        [AdminController::class, 'show']);
Route::put('/admin/{id}',        [AdminController::class, 'update']);
Route::delete('/admin/{id}',     [AdminController::class, 'destroy']); 
Route::get('/admin/search/{id}', [AdminController::class, 'searchAdmin']);
Route::post('/admin/login',     [AdminController::class, 'login']);

// Sector Codes

Route::get('/sector-codes',                            [SectorCodeController::class, 'index']);
Route::post('/sector-codes/store',                     [SectorCodeController::class, 'store']);
Route::put('/sector-codes/update/{id}',                [SectorCodeController::class, 'update']);
Route::delete('/sector-codes/delete/{id}',             [SectorCodeController::class, 'destroy']);

// Province Codes

Route::get('/province-codes',                            [ProvinceCodeController::class, 'index']);
Route::post('/province-codes/store',                     [ProvinceCodeController::class, 'store']);
Route::put('/province-codes/update/{id}',                [ProvinceCodeController::class, 'update']);
Route::delete('/province-codes/delete/{id}',             [ProvinceCodeController::class, 'destroy']);

// Age Codes

Route::get('/age-codes',                            [AgeCodeController::class, 'index']);
Route::post('/age-codes/store',                     [AgeCodeController::class, 'store']);
Route::put('/age-codes/update/{id}',                [AgeCodeController::class, 'update']);
Route::delete('/age-codes/delete/{id}',             [AgeCodeController::class, 'destroy']);

// Diagnosis Groups

Route::get('/diagnosis-groups',                           [DiagnosisGroupController::class, 'index']);
Route::post('/diagnosis-groups/store',                    [DiagnosisGroupController::class, 'store']);
Route::put('/diagnosis-groups/update/{id}',               [DiagnosisGroupController::class, 'update']);
Route::delete('/diagnosis-groups/delete/{id}',            [DiagnosisGroupController::class, 'destroy']);

// Months

Route::get('/months',                               [MonthController::class, 'index']);
Route::post('/months/store',                        [MonthController::class, 'store']);
Route::put('/months/update/{id}',                   [MonthController::class, 'update']);
Route::delete('/months/delete/{id}',                [MonthController::class, 'destroy']);

// Occupation Groups

Route::get('/occupation-groups',                                [OccupationGroupController::class, 'index']);
Route::post('/occupation-groups/store',                         [OccupationGroupController::class, 'store']);
Route::put('/occupation-groups/update/{id}',                    [OccupationGroupController::class, 'update']);
Route::delete('/occupation-groups/delete/{id}',                 [OccupationGroupController::class, 'destroy']);

// Deviations

Route::get('/deviations',                                       [DeviationController::class, 'index']);
Route::post('/deviations/store',                                [DeviationController::class, 'store']);
Route::put('/deviations/update/{id}',                           [DeviationController::class, 'update']);
Route::delete('/deviations/delete/{id}',                        [DeviationController::class, 'destroy']);

// General Activities

Route::get('/general-activities',                               [GeneralActivityController::class, 'index']);
Route::post('/general-activities/store',                        [GeneralActivityController::class, 'store']);
Route::put('/general-activities/update/{id}',                   [GeneralActivityController::class, 'update']);
Route::delete('/general-activities/delete/{id}',                [GeneralActivityController::class, 'destroy']);

// Special Activities

Route::get('/special-activities-before-accidents',                  [SpecialActivitiesBeforeAccidentController::class, 'index']);
Route::post('/special-activities-before-accidents/store',           [SpecialActivitiesBeforeAccidentController::class, 'store']);
Route::put('/special-activities-before-accidents/update/{id}',      [SpecialActivitiesBeforeAccidentController::class, 'update']);
Route::delete('/special-activities-before-accidents/delete/{id}',   [SpecialActivitiesBeforeAccidentController::class, 'destroy']);


// Injury Causes

Route::get('/injury-causes',                                    [InjuryCauseController::class, 'index']);
Route::post('/injury-causes/store',                             [InjuryCauseController::class, 'store']);
Route::put('/injury-causes/update/{id}',                        [InjuryCauseController::class, 'update']);
Route::delete('/injury-causes/delete/{id}',                     [InjuryCauseController::class, 'destroy']);

// Injury Locations

Route::get('/injury-locations',                                 [InjuryLocationController::class, 'index']);
Route::post('/injury-locations/store',                          [InjuryLocationController::class, 'store']);
Route::put('/injury-locations/update/{id}',                     [InjuryLocationController::class, 'update']);
Route::delete('/injury-locations/delete/{id}',                  [InjuryLocationController::class, 'destroy']);

// Injury Type

Route::get('/injury-types',                                     [InjuryTypeController::class, 'index']);
Route::post('/injury-types/store',                              [InjuryTypeController::class, 'store']);
Route::put('/injury-types/update/{id}',                         [InjuryTypeController::class, 'update']);
Route::delete('/injury-types/delete/{id}',                      [InjuryTypeController::class, 'destroy']);

// Materials

Route::get('/materials',                                        [MaterialController::class, 'index']);
Route::post('/materials/store',                                 [MaterialController::class, 'store']);
Route::put('/materials/update/{id}',                            [MaterialController::class, 'update']);
Route::delete('/materials/delete/{id}',                         [MaterialController::class, 'destroy']);

// Work Environment

Route::get('/work-environments',                                [WorkEnvironmentController::class, 'index']);
Route::post('/work-environments/store',                         [WorkEnvironmentController::class, 'store']);
Route::put('/work-environments/update/{id}',                    [WorkEnvironmentController::class, 'update']);
Route::delete('/work-environments/delete/{id}',                 [WorkEnvironmentController::class, 'destroy']);

// Workstation Types

Route::get('/workstation-types',                                [WorkstationTypeController::class, 'index']);
Route::post('/workstation-types/store',                         [WorkstationTypeController::class, 'store']);
Route::put('/workstation-types/update/{id}',                    [WorkstationTypeController::class, 'update']);
Route::delete('/workstation-types/delete/{id}',                 [WorkstationTypeController::class, 'destroy']);

// Time Intervals

Route::get('/time-intervals',                                   [TimeIntervalController::class, 'index']);
Route::post('/time-intervals/store',                            [TimeIntervalController::class, 'store']);
Route::put('/time-intervals/update/{id}',                       [TimeIntervalController::class, 'update']);
Route::delete('/time-intervals/delete/{id}',                    [TimeIntervalController::class, 'destroy']);

// Employee Groups

Route::get('/employee-groups',                                  [EmployeeGroupController::class, 'index']);
Route::post('/employee-groups/store',                           [EmployeeGroupController::class, 'store']);
Route::put('/employee-groups/update/{id}',                      [EmployeeGroupController::class, 'update']);
Route::delete('/employee-groups/delete/{id}',                   [EmployeeGroupController::class, 'destroy']);

// Employee Employment Duration

Route::get('/employee-employment-durations',                    [EmployeeEmploymentDurationController::class, 'index']);
Route::post('/employee-employment-durations/store',             [EmployeeEmploymentDurationController::class, 'store']);
Route::put('/employee-employment-durations/update/{id}',        [EmployeeEmploymentDurationController::class, 'update']);
Route::delete('/employee-employment-durations/delete/{id}',     [EmployeeEmploymentDurationController::class, 'destroy']);


// Work Accidents By Sector

Route::get('/work-accidents-by-sector',                                         [WorkAccidentsBySectorController::class, 'index']);
Route::get('/work-accidents-by-sector/year/{year}',                             [WorkAccidentsBySectorController::class, 'indexByYear']);
Route::get('/work-accidents-by-sector/group/{groupId}',                         [WorkAccidentsBySectorController::class, 'indexByGroupId']);
Route::get('/work-accidents-by-sector/sector-code/{sectorCode}',                [WorkAccidentsBySectorController::class, 'indexBySectorCode']);
Route::get('/work-accidents-by-sector/group-code/{groupCode}',                  [WorkAccidentsBySectorController::class, 'indexByGroupCode']);
Route::get('/work-accidents-by-sector/sub-group-code/{subGroupCode}',           [WorkAccidentsBySectorController::class, 'indexBySubGroupCode']);
Route::get('/work-accidents-by-sector/pure-code/{pureCode}',                    [WorkAccidentsBySectorController::class, 'indexByPureCode']);
Route::post('/work-accidents-by-sector/store',                                  [WorkAccidentsBySectorController::class, 'store']);
Route::post('/work-accidents-by-sector/import',                                 [WorkAccidentsBySectorController::class, 'import']);
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
Route::post('/fatal-work-accidents-by-sector/import',                           [FatalWorkAccidentsBySectorController::class, 'import']);
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
Route::post('/temporary-disability-day-by-sector/import',                       [TemporaryDisabilityDaysBySectorController::class, 'import']);
Route::put('/temporary-disability-day-by-sector/update/{id}',                   [TemporaryDisabilityDaysBySectorController::class, 'update']);
Route::delete('/temporary-disability-day-by-sector/delete/{id}',                [TemporaryDisabilityDaysBySectorController::class, 'destroy']);

// Work Accidents By Province

Route::get('/work-accidents-by-province',                                       [WorkAccidentsByProvinceController::class, 'index']);
Route::get('/work-accidents-by-province/year/{year}',                           [WorkAccidentsByProvinceController::class, 'indexByYear']);
Route::get('/work-accidents-by-province/province-id/{provinceId}',              [WorkAccidentsByProvinceController::class, 'indexByProvinceId']);
Route::get('/work-accidents-by-province/province-code/{provinceCode}',          [WorkAccidentsByProvinceController::class, 'indexByProvinceCode']);
Route::post('/work-accidents-by-province/store',                                [WorkAccidentsByProvinceController::class, 'store']);
Route::post('/work-accidents-by-province/import',                               [WorkAccidentsByProvinceController::class, 'import']);
Route::put('/work-accidents-by-province/update/{id}',                           [WorkAccidentsByProvinceController::class, 'update']);
Route::delete('/work-accidents-by-province/delete/{id}',                        [WorkAccidentsByProvinceController::class, 'destroy']);

// Fatal Work Accidents By Province

Route::get('/fatal-work-accidents-by-province',                                  [FatalWorkAccidentsByProvinceController::class, 'index']);
Route::get('/fatal-work-accidents-by-province/year/{year}',                      [FatalWorkAccidentsByProvinceController::class, 'indexByYear']);
Route::get('/fatal-work-accidents-by-province/province-id/{provinceId}',         [FatalWorkAccidentsByProvinceController::class, 'indexByProvinceId']);
Route::get('/fatal-work-accidents-by-province/province-code/{provinceCode}',     [FatalWorkAccidentsByProvinceController::class, 'indexByProvinceCode']);
Route::post('/fatal-work-accidents-by-province/store',                           [FatalWorkAccidentsByProvinceController::class, 'store']);
Route::post('/fatal-work-accidents-by-province/import',                          [FatalWorkAccidentsByProvinceController::class, 'import']);
Route::put('/fatal-work-accidents-by-province/update/{id}',                      [FatalWorkAccidentsByProvinceController::class, 'update']);
Route::delete('/fatal-work-accidents-by-province/delete/{id}',                   [FatalWorkAccidentsByProvinceController::class, 'destroy']);

// Temporary Disability Day By Province

Route::get('/temporary-disability-day-by-province',                                    [TemporaryDisabilityDaysByProvinceController::class, 'index']);
Route::get('/temporary-disability-day-by-province/year/{year}',                        [TemporaryDisabilityDaysByProvinceController::class, 'indexByYear']);
Route::get('/temporary-disability-day-by-province/province/{provinceId}',              [TemporaryDisabilityDaysByProvinceController::class, 'indexByProvinceId']);
Route::get('/temporary-disability-day-by-province/province-code/{provinceCode}',       [TemporaryDisabilityDaysByProvinceController::class, 'indexByProvinceCode']);
Route::post('/temporary-disability-day-by-province/store',                             [TemporaryDisabilityDaysByProvinceController::class, 'store']);
Route::post('/temporary-disability-day-by-province/import',                            [TemporaryDisabilityDaysByProvinceController::class, 'import']);
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

// Work Accidents By Age

Route::get('/work-accidents-by-age',                            [WorkAccidentsByAgeController::class, 'index']);
Route::get('/work-accidents-by-age/year/{year}',                [WorkAccidentsByAgeController::class, 'indexByYear']);
Route::get('/work-accidents-by-age/age/{ageId}',                [WorkAccidentsByAgeController::class, 'indexByAge']);
Route::post('/work-accidents-by-age/store',                     [WorkAccidentsByAgeController::class, 'store']);
Route::post('/work-accidents-by-age/import',                    [WorkAccidentsByAgeController::class, 'import']);
Route::put('/work-accidents-by-age/update/{id}',                [WorkAccidentsByAgeController::class, 'update']);
Route::delete('/work-accidents-by-age/delete/{id}',             [WorkAccidentsByAgeController::class, 'destroy']);

// Fatal Work Accidents By Age

Route::get('/fatal-work-accidents-by-age',                      [FatalWorkAccidentsByAgeController::class, 'index']);
Route::get('/fatal-work-accidents-by-age/year/{year}',          [FatalWorkAccidentsByAgeController::class, 'indexByYear']);
Route::get('/fatal-work-accidents-by-age/age/{ageId}',          [FatalWorkAccidentsByAgeController::class, 'indexByAge']);
Route::post('/fatal-work-accidents-by-age/store',               [FatalWorkAccidentsByAgeController::class, 'store']);
Route::post('/fatal-work-accidents-by-age/import',              [FatalWorkAccidentsByAgeController::class, 'import']);
Route::put('/fatal-work-accidents-by-age/update/{id}',          [FatalWorkAccidentsByAgeController::class, 'update']);
Route::delete('/fatal-work-accidents-by-age/delete/{id}',       [FatalWorkAccidentsByAgeController::class, 'destroy']);

// Occupational Disease By Diagnosis

Route::get('/occupational-disease-by-diagnosis',                                [OccupationalDiseaseByDiagnosisController::class, 'index']);
Route::get('/occupational-disease-by-diagnosis/year/{year}',                    [OccupationalDiseaseByDiagnosisController::class, 'indexByYear']);
Route::get('/occupational-disease-by-diagnosis/gender/{gender}',                [OccupationalDiseaseByDiagnosisController::class, 'indexByGender']);
Route::get('/occupational-disease-by-diagnosis/group-code/{groupCode}',         [OccupationalDiseaseByDiagnosisController::class, 'indexByGroupCode']);
Route::get('/occupational-disease-by-diagnosis/sub-group-code/{subGroupCode}',  [OccupationalDiseaseByDiagnosisController::class, 'indexBySubGroupCode']);
Route::post('/occupational-disease-by-diagnosis/store',                         [OccupationalDiseaseByDiagnosisController::class, 'store']);
Route::put('/occupational-disease-by-diagnosis/update/{id}',                    [OccupationalDiseaseByDiagnosisController::class, 'update']);
Route::delete('/occupational-disease-by-diagnosis/delete/{id}',                 [OccupationalDiseaseByDiagnosisController::class, 'destroy']);

// Work Accidents By Month

Route::get('/work-accidents-by-month',                      [WorkAccidentsByMonthController::class, 'index']);
Route::get('/work-accidents-by-month/year/{year}',          [WorkAccidentsByMonthController::class, 'indexByYear']);
Route::get('/work-accidents-by-month/gender/{gender}',      [WorkAccidentsByMonthController::class, 'indexByGender']);
Route::get('/work-accidents-by-month/month/{monthId}',      [WorkAccidentsByMonthController::class, 'indexByMonth']);
Route::post('/work-accidents-by-month/store',               [WorkAccidentsByMonthController::class, 'store']);
Route::post('/work-accidents-by-month/import',              [WorkAccidentsByMonthController::class, 'import']);
Route::put('/work-accidents-by-month/update/{id}',          [WorkAccidentsByMonthController::class, 'update']);
Route::delete('/work-accidents-by-month/delete/{id}',       [WorkAccidentsByMonthController::class, 'destroy']);

// Temporary Disability Days By Month

Route::get('/temporary-disability-days-by-month',                       [TemporaryDisabilityDaysByMonthController::class, 'index']);
Route::get('/temporary-disability-days-by-month/year/{year}',           [TemporaryDisabilityDaysByMonthController::class, 'indexByYear']);
Route::get('/temporary-disability-days-by-month/gender/{gender}',       [TemporaryDisabilityDaysByMonthController::class, 'indexByGender']);
Route::get('/temporary-disability-days-by-month/month/{monthId}',       [TemporaryDisabilityDaysByMonthController::class, 'indexByMonth']);
Route::post('/temporary-disability-days-by-month/store',                [TemporaryDisabilityDaysByMonthController::class, 'store']);
Route::post('/temporary-disability-days-by-month/import',               [TemporaryDisabilityDaysByMonthController::class, 'import']);
Route::put('/temporary-disability-days-by-month/update/{id}',           [TemporaryDisabilityDaysByMonthController::class, 'update']);
Route::delete('/temporary-disability-days-by-month/delete/{id}',        [TemporaryDisabilityDaysByMonthController::class, 'destroy']);

// Fatal Work Accidents By Month

Route::get('/fatal-work-accidents-by-month',                            [FatalWorkAccidentsByMonthController::class, 'index']);
Route::get('/fatal-work-accidents-by-month/year/{year}',                [FatalWorkAccidentsByMonthController::class, 'indexByYear']);
Route::get('/fatal-work-accidents-by-month/gender/{gender}',            [FatalWorkAccidentsByMonthController::class, 'indexByGender']);
Route::get('/fatal-work-accidents-by-month/month/{monthId}',            [FatalWorkAccidentsByMonthController::class, 'indexByMonth']);
Route::post('/fatal-work-accidents-by-month/store',                     [FatalWorkAccidentsByMonthController::class, 'store']);
Route::post('/fatal-work-accidents-by-month/import',                    [FatalWorkAccidentsByMonthController::class, 'import']);
Route::put('/fatal-work-accidents-by-month/update/{id}',                [FatalWorkAccidentsByMonthController::class, 'update']);
Route::delete('/fatal-work-accidents-by-month/delete/{id}',             [FatalWorkAccidentsByMonthController::class, 'destroy']);

// Accidents And Fatalities By Occupation Controller

Route::get('/accidents-and-fatalities-by-occupation',                                       [AccidentsAndFatalitiesByOccupationController::class, 'index']);
Route::get('/accidents-and-fatalities-by-occupation/year/{year}',                           [AccidentsAndFatalitiesByOccupationController::class, 'indexByYear']);
Route::get('/accidents-and-fatalities-by-occupation/group-id/{groupId}',                    [AccidentsAndFatalitiesByOccupationController::class, 'indexByGroupId']);
Route::get('/accidents-and-fatalities-by-occupation/gender/{gender}',                       [AccidentsAndFatalitiesByOccupationController::class, 'indexByGender']);
Route::get('/accidents-and-fatalities-by-occupation/code/{code}',                           [AccidentsAndFatalitiesByOccupationController::class, 'indexByCode']);
Route::get('/accidents-and-fatalities-by-occupation/occupation-code/{occupationCode}',      [AccidentsAndFatalitiesByOccupationController::class, 'indexByOccupationCode']);
Route::get('/accidents-and-fatalities-by-occupation/group-code/{groupCode}',                [AccidentsAndFatalitiesByOccupationController::class, 'indexByGroupCode']);
Route::get('/accidents-and-fatalities-by-occupation/sub-group-code/{subGroupCode}',         [AccidentsAndFatalitiesByOccupationController::class, 'indexBySubGroupCode']);
Route::get('/accidents-and-fatalities-by-occupation/pure-code/{pureCode}',                  [AccidentsAndFatalitiesByOccupationController::class, 'indexByPureCode']);
Route::post('/accidents-and-fatalities-by-occupation/store',                                [AccidentsAndFatalitiesByOccupationController::class, 'store']);
Route::put('/accidents-and-fatalities-by-occupation/update/{id}',                           [AccidentsAndFatalitiesByOccupationController::class, 'update']);
Route::delete('/accidents-and-fatalities-by-occupation/delete/{id}',                        [AccidentsAndFatalitiesByOccupationController::class, 'destroy']);

// Occ Disease Fatalities By Occupation

Route::get('/occ-disease-fatalities-by-occupation',                                         [OccDiseaseFatalitiesByOccupationController::class, 'index']);
Route::get('/occ-disease-fatalities-by-occupation/year/{year}',                             [OccDiseaseFatalitiesByOccupationController::class, 'indexByYear']);
Route::get('/occ-disease-fatalities-by-occupation/group-id/{groupId}',                      [OccDiseaseFatalitiesByOccupationController::class, 'indexByGroupId']);
Route::get('/occ-disease-fatalities-by-occupation/gender/{gender}',                         [OccDiseaseFatalitiesByOccupationController::class, 'indexByGender']);
Route::get('/occ-disease-fatalities-by-occupation/code/{code}',                             [OccDiseaseFatalitiesByOccupationController::class, 'indexByCode']);
Route::get('/occ-disease-fatalities-by-occupation/occupation-code/{occupationCode}',        [OccDiseaseFatalitiesByOccupationController::class, 'indexByOccupationCode']);
Route::get('/occ-disease-fatalities-by-occupation/group-code/{groupCode}',                  [OccDiseaseFatalitiesByOccupationController::class, 'indexByGroupCode']);
Route::get('/occ-disease-fatalities-by-occupation/sub-group-code/{subGroupCode}',           [OccDiseaseFatalitiesByOccupationController::class, 'indexBySubGroupCode']);
Route::get('/occ-disease-fatalities-by-occupation/pure-code/{pureCode}',                    [OccDiseaseFatalitiesByOccupationController::class, 'indexByPureCode']);
Route::post('/occ-disease-fatalities-by-occupation/store',                                  [OccDiseaseFatalitiesByOccupationController::class, 'store']);
Route::put('/occ-disease-fatalities-by-occupation/update/{id}',                             [OccDiseaseFatalitiesByOccupationController::class, 'update']);
Route::delete('/occ-disease-fatalities-by-occupation/delete/{id}',                          [OccDiseaseFatalitiesByOccupationController::class, 'destroy']);

// Accidents And Fatalities By Injury

Route::get('/accidents-and-fatalities-by-injury',                                           [AccidentsAndFatalitiesByInjuryController::class, 'index']);
Route::get('/accidents-and-fatalities-by-injury/year/{year}',                               [AccidentsAndFatalitiesByInjuryController::class, 'indexByYear']);
Route::get('/accidents-and-fatalities-by-injury/group-id/{groupId}',                        [AccidentsAndFatalitiesByInjuryController::class, 'indexByGroupId']);
Route::get('/accidents-and-fatalities-by-injury/gender/{gender}',                           [AccidentsAndFatalitiesByInjuryController::class, 'indexByGender']);
Route::get('/accidents-and-fatalities-by-injury/injury-code/{injuryCode}',                  [AccidentsAndFatalitiesByInjuryController::class, 'indexByInjuryCode']);
Route::get('/accidents-and-fatalities-by-injury/group-code/{groupCode}',                    [AccidentsAndFatalitiesByInjuryController::class, 'indexByGroupCode']);
Route::get('/accidents-and-fatalities-by-injury/sub-group-code/{subGroupCode}',             [AccidentsAndFatalitiesByInjuryController::class, 'indexBySubGroupCode']);
Route::post('/accidents-and-fatalities-by-injury/store',                                    [AccidentsAndFatalitiesByInjuryController::class, 'store']);
Route::put('/accidents-and-fatalities-by-injury/update/{id}',                               [AccidentsAndFatalitiesByInjuryController::class, 'update']);
Route::delete('/accidents-and-fatalities-by-injury/delete/{id}',                            [AccidentsAndFatalitiesByInjuryController::class, 'destroy']);



// Accidents And Fatalities By Injury Locations

Route::get('/accidents-and-fatalities-by-injury-locations',                                           [AccidentsAndFatalitiesByInjuryLocationController::class, 'index']);
Route::get('/accidents-and-fatalities-by-injury-locations/year/{year}',                               [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexByYear']);
Route::get('/accidents-and-fatalities-by-injury-locations/group-id/{groupId}',                        [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexByGroupId']);
Route::get('/accidents-and-fatalities-by-injury-locations/gender/{gender}',                           [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexByGender']);
Route::get('/accidents-and-fatalities-by-injury-locations/injury-code/{injuryCode}',                  [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexByInjuryCode']);
Route::get('/accidents-and-fatalities-by-injury-locations/group-code/{groupCode}',                    [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexByGroupCode']);
Route::get('/accidents-and-fatalities-by-injury-locations/sub-group-code/{subGroupCode}',             [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexBySubGroupCode']);
Route::post('/accidents-and-fatalities-by-injury-locations/store',                                    [AccidentsAndFatalitiesByInjuryLocationController::class, 'store']);
Route::put('/accidents-and-fatalities-by-injury-locations/update/{id}',                               [AccidentsAndFatalitiesByInjuryLocationController::class, 'update']);
Route::delete('/accidents-and-fatalities-by-injury-locations/delete/{id}',                            [AccidentsAndFatalitiesByInjuryLocationController::class, 'destroy']);




// Accidents And Fatalities By Injury Causes

Route::get('/accidents-and-fatalities-by-injury-causes',                                           [AccidentsAndFatalitiesByInjuryCauseController::class, 'index']);
Route::get('/accidents-and-fatalities-by-injury-causes/year/{year}',                               [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexByYear']);
Route::get('/accidents-and-fatalities-by-injury-causes/group-id/{groupId}',                        [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexByGroupId']);
Route::get('/accidents-and-fatalities-by-injury-causes/gender/{gender}',                           [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexByGender']);
Route::get('/accidents-and-fatalities-by-injury-causes/injury-code/{injuryCode}',                  [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexByInjuryCode']);
Route::get('/accidents-and-fatalities-by-injury-causes/group-code/{groupCode}',                    [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexByGroupCode']);
Route::get('/accidents-and-fatalities-by-injury-causes/sub-group-code/{subGroupCode}',             [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexBySubGroupCode']);
Route::post('/accidents-and-fatalities-by-injury-causes/store',                                    [AccidentsAndFatalitiesByInjuryCauseController::class, 'store']);
Route::put('/accidents-and-fatalities-by-injury-causes/update/{id}',                               [AccidentsAndFatalitiesByInjuryCauseController::class, 'update']);
Route::delete('/accidents-and-fatalities-by-injury-causes/delete/{id}',                            [AccidentsAndFatalitiesByInjuryCauseController::class, 'destroy']);
