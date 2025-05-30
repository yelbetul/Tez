<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AccidentsAndFatalitiesByInjuryController;
    use App\Http\Controllers\AccidentsAndFatalitiesByOccupationController;
    use App\Http\Controllers\AccidentsAndFatalitiesByInjuryLocationController;
    use App\Http\Controllers\AccidentsAndFatalitiesByInjuryCauseController;
    use App\Http\Controllers\AccidentsAndFatalitiesByGeneralActivityController;
    use App\Http\Controllers\AccidentsAndFatalitiesByDeviationController;
    use App\Http\Controllers\AccidentsAndFatalitiesByMaterialController;
    use App\Http\Controllers\AccidentsAndFatalitiesByEnvironmentController;
    use App\Http\Controllers\AccidentsAndFatalitiesByEmployeeController;
    use App\Http\Controllers\AccidentsAndFatalitiesByEmployerDurationController;
    use App\Http\Controllers\AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController;
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
    use App\Http\Controllers\OccDiseaseFatalitiesByEmployeeController;
    use App\Http\Controllers\OccDiseaseFatalitiesByOccupationController;
    use App\Http\Controllers\OccDiseaseFatalitiesByEmployerDurationController;
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
    use App\Http\Controllers\WorkAccidentByWorkstationController;
    use App\Http\Controllers\WorkAccidentByHourController;
    use App\Http\Controllers\WorkEnvironmentController;
    use App\Http\Controllers\WorkstationTypeController;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\OccupationStatsController;


    Route::get('occupations/summary/{year}', [OccupationStatsController::class, 'summary']);



    // Admins

    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin/store', [AdminController::class, 'store']);
    Route::get('/admin/{id}', [AdminController::class, 'show']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
    Route::get('/admin/search/{id}', [AdminController::class, 'searchAdmin']);
    Route::post('/admin/login', [AdminController::class, 'login']);

    // Sector Codes

    Route::get('/sector-codes', [SectorCodeController::class, 'index']);
    Route::get('/sectors-user', [SectorCodeController::class, 'indexUser']);
    Route::post('/sector-codes/store', [SectorCodeController::class, 'store']);
    Route::put('/sector-codes/update/{id}', [SectorCodeController::class, 'update']);
    Route::delete('/sector-codes/delete/{id}', [SectorCodeController::class, 'destroy']);

    // Province Codes

    Route::get('/province-codes', [ProvinceCodeController::class, 'index']);
    Route::get('/provinces-user', [ProvinceCodeController::class, 'indexUser']);
    Route::post('/province-codes/store', [ProvinceCodeController::class, 'store']);
    Route::put('/province-codes/update/{id}', [ProvinceCodeController::class, 'update']);
    Route::delete('/province-codes/delete/{id}', [ProvinceCodeController::class, 'destroy']);

    // Age Codes

    Route::get('/age-codes', [AgeCodeController::class, 'index']);
    Route::get('/ages-user', [AgeCodeController::class, 'indexUser']);
    Route::post('/age-codes/store', [AgeCodeController::class, 'store']);
    Route::put('/age-codes/update/{id}', [AgeCodeController::class, 'update']);
    Route::delete('/age-codes/delete/{id}', [AgeCodeController::class, 'destroy']);

    // Diagnosis Groups

    Route::get('/diagnosis-groups', [DiagnosisGroupController::class, 'index']);
    Route::get('/diagnosis-user', [DiagnosisGroupController::class, 'indexUser']);
    Route::post('/diagnosis-groups/store', [DiagnosisGroupController::class, 'store']);
    Route::put('/diagnosis-groups/update/{id}', [DiagnosisGroupController::class, 'update']);
    Route::delete('/diagnosis-groups/delete/{id}', [DiagnosisGroupController::class, 'destroy']);

    // Months

    Route::get('/months', [MonthController::class, 'index']);
    Route::post('/months/store', [MonthController::class, 'store']);
    Route::put('/months/update/{id}', [MonthController::class, 'update']);
    Route::delete('/months/delete/{id}', [MonthController::class, 'destroy']);

    // Occupation Groups

    Route::get('/occupation-groups', [OccupationGroupController::class, 'index']);
    Route::get('/occupations-user', [OccupationGroupController::class, 'indexUser']);
    Route::post('/occupation-groups/store', [OccupationGroupController::class, 'store']);
    Route::put('/occupation-groups/update/{id}', [OccupationGroupController::class, 'update']);
    Route::delete('/occupation-groups/delete/{id}', [OccupationGroupController::class, 'destroy']);

    // Deviations

    Route::get('/deviations', [DeviationController::class, 'index']);
    Route::get('/deviations-user', [DeviationController::class, 'indexUser']);
    Route::post('/deviations/store', [DeviationController::class, 'store']);
    Route::put('/deviations/update/{id}', [DeviationController::class, 'update']);
    Route::delete('/deviations/delete/{id}', [DeviationController::class, 'destroy']);

    // General Activities

    Route::get('/general-activities', [GeneralActivityController::class, 'index']);
    Route::get('/general-activities-user', [GeneralActivityController::class, 'indexUser']);
    Route::post('/general-activities/store', [GeneralActivityController::class, 'store']);
    Route::put('/general-activities/update/{id}', [GeneralActivityController::class, 'update']);
    Route::delete('/general-activities/delete/{id}', [GeneralActivityController::class, 'destroy']);

    // Special Activities

    Route::get('/special-activities-before-accidents', [SpecialActivitiesBeforeAccidentController::class, 'index']);
    Route::get('/special-activities-user', [SpecialActivitiesBeforeAccidentController::class, 'indexUser']);
    Route::post('/special-activities-before-accidents/store', [SpecialActivitiesBeforeAccidentController::class, 'store']);
    Route::put('/special-activities-before-accidents/update/{id}', [SpecialActivitiesBeforeAccidentController::class, 'update']);
    Route::delete('/special-activities-before-accidents/delete/{id}', [SpecialActivitiesBeforeAccidentController::class, 'destroy']);


    // Injury Causes

    Route::get('/injury-causes', [InjuryCauseController::class, 'index']);
    Route::get('/injury-causes-user', [InjuryCauseController::class, 'indexUser']);
    Route::post('/injury-causes/store', [InjuryCauseController::class, 'store']);
    Route::put('/injury-causes/update/{id}', [InjuryCauseController::class, 'update']);
    Route::delete('/injury-causes/delete/{id}', [InjuryCauseController::class, 'destroy']);

    // Injury Locations

    Route::get('/injury-locations', [InjuryLocationController::class, 'index']);
    Route::get('/injury-locations-user', [InjuryLocationController::class, 'indexUser']);
    Route::post('/injury-locations/store', [InjuryLocationController::class, 'store']);
    Route::put('/injury-locations/update/{id}', [InjuryLocationController::class, 'update']);
    Route::delete('/injury-locations/delete/{id}', [InjuryLocationController::class, 'destroy']);

    // Injury Type

    Route::get('/injury-types', [InjuryTypeController::class, 'index']);
    Route::get('/injury-types-user', [InjuryTypeController::class, 'indexUser']);
    Route::post('/injury-types/store', [InjuryTypeController::class, 'store']);
    Route::put('/injury-types/update/{id}', [InjuryTypeController::class, 'update']);
    Route::delete('/injury-types/delete/{id}', [InjuryTypeController::class, 'destroy']);

    // Materials

    Route::get('/materials', [MaterialController::class, 'index']);
    Route::get('/materials-user', [MaterialController::class, 'indexUser']);
    Route::post('/materials/store', [MaterialController::class, 'store']);
    Route::put('/materials/update/{id}', [MaterialController::class, 'update']);
    Route::delete('/materials/delete/{id}', [MaterialController::class, 'destroy']);

    // Work Environment

    Route::get('/work-environments', [WorkEnvironmentController::class, 'index']);
    Route::get('/work-environments-user', [WorkEnvironmentController::class, 'indexUser']);
    Route::post('/work-environments/store', [WorkEnvironmentController::class, 'store']);
    Route::put('/work-environments/update/{id}', [WorkEnvironmentController::class, 'update']);
    Route::delete('/work-environments/delete/{id}', [WorkEnvironmentController::class, 'destroy']);

    // Workstation Types

    Route::get('/workstation-types', [WorkstationTypeController::class, 'index']);
    Route::get('/workstation-types-user', [WorkstationTypeController::class, 'indexUser']);
    Route::post('/workstation-types/store', [WorkstationTypeController::class, 'store']);
    Route::put('/workstation-types/update/{id}', [WorkstationTypeController::class, 'update']);
    Route::delete('/workstation-types/delete/{id}', [WorkstationTypeController::class, 'destroy']);

    // Time Intervals

    Route::get('/time-intervals', [TimeIntervalController::class, 'index']);
    Route::get('/time-intervals-user', [TimeIntervalController::class, 'indexUser']);
    Route::post('/time-intervals/store', [TimeIntervalController::class, 'store']);
    Route::put('/time-intervals/update/{id}', [TimeIntervalController::class, 'update']);
    Route::delete('/time-intervals/delete/{id}', [TimeIntervalController::class, 'destroy']);

    // Employee Groups

    Route::get('/employee-groups', [EmployeeGroupController::class, 'index']);
    Route::get('/employee-groups-user', [EmployeeGroupController::class, 'indexUser']);
    Route::post('/employee-groups/store', [EmployeeGroupController::class, 'store']);
    Route::put('/employee-groups/update/{id}', [EmployeeGroupController::class, 'update']);
    Route::delete('/employee-groups/delete/{id}', [EmployeeGroupController::class, 'destroy']);

    // Employee Employment Duration

    Route::get('/employee-employment-durations', [EmployeeEmploymentDurationController::class, 'index']);
    Route::get('/employee-employment-durations-user', [EmployeeEmploymentDurationController::class, 'indexUser']);
    Route::post('/employee-employment-durations/store', [EmployeeEmploymentDurationController::class, 'store']);
    Route::put('/employee-employment-durations/update/{id}', [EmployeeEmploymentDurationController::class, 'update']);
    Route::delete('/employee-employment-durations/delete/{id}', [EmployeeEmploymentDurationController::class, 'destroy']);


    // Work Accidents By Sector

    Route::get('/work-accidents-by-sector', [WorkAccidentsBySectorController::class, 'index']);
    Route::get('/work-accidents-by-sector-user', [WorkAccidentsBySectorController::class, 'indexUser']);
    Route::post('/work-accidents-by-sector/store', [WorkAccidentsBySectorController::class, 'store']);
    Route::post('/work-accidents-by-sector/import', [WorkAccidentsBySectorController::class, 'import']);
    Route::put('/work-accidents-by-sector/update/{id}', [WorkAccidentsBySectorController::class, 'update']);
    Route::delete('/work-accidents-by-sector/delete/{id}', [WorkAccidentsBySectorController::class, 'destroy']);

    // Fatal Work Accidents By Sector

    Route::get('/fatal-work-accidents-by-sector', [FatalWorkAccidentsBySectorController::class, 'index']);
    Route::get('/fatal-work-accidents-by-sector-user', [FatalWorkAccidentsBySectorController::class, 'indexUser']);
    Route::post('/fatal-work-accidents-by-sector/store', [FatalWorkAccidentsBySectorController::class, 'store']);
    Route::post('/fatal-work-accidents-by-sector/import', [FatalWorkAccidentsBySectorController::class, 'import']);
    Route::put('/fatal-work-accidents-by-sector/update/{id}', [FatalWorkAccidentsBySectorController::class, 'update']);
    Route::delete('/fatal-work-accidents-by-sector/delete/{id}', [FatalWorkAccidentsBySectorController::class, 'destroy']);

    // Temporary Disability Day By Sector

    Route::get('/temporary-disability-day-by-sector', [TemporaryDisabilityDaysBySectorController::class, 'index']);
    Route::get('/temporary-disability-day-by-sector-user', [TemporaryDisabilityDaysBySectorController::class, 'indexUser']);
    Route::post('/temporary-disability-day-by-sector/store', [TemporaryDisabilityDaysBySectorController::class, 'store']);
    Route::post('/temporary-disability-day-by-sector/import', [TemporaryDisabilityDaysBySectorController::class, 'import']);
    Route::put('/temporary-disability-day-by-sector/update/{id}', [TemporaryDisabilityDaysBySectorController::class, 'update']);
    Route::delete('/temporary-disability-day-by-sector/delete/{id}', [TemporaryDisabilityDaysBySectorController::class, 'destroy']);

    // Work Accidents By Province

    Route::get('/work-accidents-by-province', [WorkAccidentsByProvinceController::class, 'index']);
    Route::get('/work-accidents-by-province-user', [WorkAccidentsByProvinceController::class, 'indexUser']);
    Route::post('/work-accidents-by-province/store', [WorkAccidentsByProvinceController::class, 'store']);
    Route::post('/work-accidents-by-province/import', [WorkAccidentsByProvinceController::class, 'import']);
    Route::put('/work-accidents-by-province/update/{id}', [WorkAccidentsByProvinceController::class, 'update']);
    Route::delete('/work-accidents-by-province/delete/{id}', [WorkAccidentsByProvinceController::class, 'destroy']);

    // Fatal Work Accidents By Province

    Route::get('/fatal-work-accidents-by-province', [FatalWorkAccidentsByProvinceController::class, 'index']);
    Route::get('/fatal-work-accidents-by-province-user', [FatalWorkAccidentsByProvinceController::class, 'indexUser']);
    Route::post('/fatal-work-accidents-by-province/store', [FatalWorkAccidentsByProvinceController::class, 'store']);
    Route::post('/fatal-work-accidents-by-province/import', [FatalWorkAccidentsByProvinceController::class, 'import']);
    Route::put('/fatal-work-accidents-by-province/update/{id}', [FatalWorkAccidentsByProvinceController::class, 'update']);
    Route::delete('/fatal-work-accidents-by-province/delete/{id}', [FatalWorkAccidentsByProvinceController::class, 'destroy']);

    // Temporary Disability Day By Province

    Route::get('/temporary-disability-day-by-province', [TemporaryDisabilityDaysByProvinceController::class, 'index']);
    Route::get('/temporary-disability-day-by-province-user', [TemporaryDisabilityDaysByProvinceController::class, 'indexUser']);
    Route::post('/temporary-disability-day-by-province/store', [TemporaryDisabilityDaysByProvinceController::class, 'store']);
    Route::post('/temporary-disability-day-by-province/import', [TemporaryDisabilityDaysByProvinceController::class, 'import']);
    Route::put('/temporary-disability-day-by-province/update/{id}', [TemporaryDisabilityDaysByProvinceController::class, 'update']);
    Route::delete('/temporary-disability-day-by-province/delete/{id}', [TemporaryDisabilityDaysByProvinceController::class, 'destroy']);

    // Disability Days Occupational Diseases By Province

    Route::get('/disability-days-occ-dis-by-province', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'index']);
    Route::get('/disability-days-occ-dis-by-province-user', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'indexUser']);
    Route::post('/disability-days-occ-dis-by-province/store', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'store']);
    Route::post('/disability-days-occ-dis-by-province/import', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'import']);
    Route::put('/disability-days-occ-dis-by-province/update/{id}', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'update']);
    Route::delete('/disability-days-occ-dis-by-province/delete/{id}', [DisabilityDaysOccupationalDiseasesByProvinceController::class, 'destroy']);

    // Work Accidents By Age

    Route::get('/work-accidents-by-age', [WorkAccidentsByAgeController::class, 'index']);
    Route::get('/work-accidents-by-age-user', [WorkAccidentsByAgeController::class, 'indexUser']);
    Route::post('/work-accidents-by-age/store', [WorkAccidentsByAgeController::class, 'store']);
    Route::post('/work-accidents-by-age/import', [WorkAccidentsByAgeController::class, 'import']);
    Route::put('/work-accidents-by-age/update/{id}', [WorkAccidentsByAgeController::class, 'update']);
    Route::delete('/work-accidents-by-age/delete/{id}', [WorkAccidentsByAgeController::class, 'destroy']);

    // Fatal Work Accidents By Age

    Route::get('/fatal-work-accidents-by-age', [FatalWorkAccidentsByAgeController::class, 'index']);
    Route::get('/fatal-work-accidents-by-age-user', [FatalWorkAccidentsByAgeController::class, 'indexUser']);
    Route::post('/fatal-work-accidents-by-age/store', [FatalWorkAccidentsByAgeController::class, 'store']);
    Route::post('/fatal-work-accidents-by-age/import', [FatalWorkAccidentsByAgeController::class, 'import']);
    Route::put('/fatal-work-accidents-by-age/update/{id}', [FatalWorkAccidentsByAgeController::class, 'update']);
    Route::delete('/fatal-work-accidents-by-age/delete/{id}', [FatalWorkAccidentsByAgeController::class, 'destroy']);

    // Occupational Disease By Diagnosis

    Route::get('/occupational-disease-by-diagnosis', [OccupationalDiseaseByDiagnosisController::class, 'index']);
    Route::get('/occupational-disease-by-diagnosis-user', [OccupationalDiseaseByDiagnosisController::class, 'indexUser']);
    Route::post('/occupational-disease-by-diagnosis/store', [OccupationalDiseaseByDiagnosisController::class, 'store']);
    Route::put('/occupational-disease-by-diagnosis/update/{id}', [OccupationalDiseaseByDiagnosisController::class, 'update']);
    Route::delete('/occupational-disease-by-diagnosis/delete/{id}', [OccupationalDiseaseByDiagnosisController::class, 'destroy']);

    // Work Accidents By Month

    Route::get('/work-accidents-by-month', [WorkAccidentsByMonthController::class, 'index']);
    Route::get('/work-accidents-by-month-user', [WorkAccidentsByMonthController::class, 'indexUser']);
    Route::post('/work-accidents-by-month/store', [WorkAccidentsByMonthController::class, 'store']);
    Route::post('/work-accidents-by-month/import', [WorkAccidentsByMonthController::class, 'import']);
    Route::put('/work-accidents-by-month/update/{id}', [WorkAccidentsByMonthController::class, 'update']);
    Route::delete('/work-accidents-by-month/delete/{id}', [WorkAccidentsByMonthController::class, 'destroy']);

    // Temporary Disability Days By Month

    Route::get('/temporary-disability-days-by-month', [TemporaryDisabilityDaysByMonthController::class, 'index']);
    Route::get('/temporary-disability-days-by-month-user', [TemporaryDisabilityDaysByMonthController::class, 'indexUser']);
    Route::post('/temporary-disability-days-by-month/store', [TemporaryDisabilityDaysByMonthController::class, 'store']);
    Route::post('/temporary-disability-days-by-month/import', [TemporaryDisabilityDaysByMonthController::class, 'import']);
    Route::put('/temporary-disability-days-by-month/update/{id}', [TemporaryDisabilityDaysByMonthController::class, 'update']);
    Route::delete('/temporary-disability-days-by-month/delete/{id}', [TemporaryDisabilityDaysByMonthController::class, 'destroy']);

    // Fatal Work Accidents By Month

    Route::get('/fatal-work-accidents-by-month', [FatalWorkAccidentsByMonthController::class, 'index']);
    Route::get('/fatal-work-accidents-by-month-user', [FatalWorkAccidentsByMonthController::class, 'indexUser']);
    Route::post('/fatal-work-accidents-by-month/store', [FatalWorkAccidentsByMonthController::class, 'store']);
    Route::post('/fatal-work-accidents-by-month/import', [FatalWorkAccidentsByMonthController::class, 'import']);
    Route::put('/fatal-work-accidents-by-month/update/{id}', [FatalWorkAccidentsByMonthController::class, 'update']);
    Route::delete('/fatal-work-accidents-by-month/delete/{id}', [FatalWorkAccidentsByMonthController::class, 'destroy']);

    // Accidents And Fatalities By Occupation Controller

    Route::get('/accidents-and-fatalities-by-occupation', [AccidentsAndFatalitiesByOccupationController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-occupation-user', [AccidentsAndFatalitiesByOccupationController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-occupation/store', [AccidentsAndFatalitiesByOccupationController::class, 'store']);
    Route::put('/accidents-and-fatalities-by-occupation/update/{id}', [AccidentsAndFatalitiesByOccupationController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-occupation/delete/{id}', [AccidentsAndFatalitiesByOccupationController::class, 'destroy']);

    // Occ Disease Fatalities By Occupation

    Route::get('/occ-disease-fatalities-by-occupation', [OccDiseaseFatalitiesByOccupationController::class, 'index']);
    Route::get('/occ-disease-fatalities-by-occupation-user', [OccDiseaseFatalitiesByOccupationController::class, 'indexUser']);
    Route::post('/occ-disease-fatalities-by-occupation/store', [OccDiseaseFatalitiesByOccupationController::class, 'store']);
    Route::put('/occ-disease-fatalities-by-occupation/update/{id}', [OccDiseaseFatalitiesByOccupationController::class, 'update']);
    Route::delete('/occ-disease-fatalities-by-occupation/delete/{id}', [OccDiseaseFatalitiesByOccupationController::class, 'destroy']);

    // Accidents And Fatalities By Injury

    Route::get('/accidents-and-fatalities-by-injury', [AccidentsAndFatalitiesByInjuryController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-injury-user', [AccidentsAndFatalitiesByInjuryController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-injury/store', [AccidentsAndFatalitiesByInjuryController::class, 'store']);
    Route::put('/accidents-and-fatalities-by-injury/update/{id}', [AccidentsAndFatalitiesByInjuryController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-injury/delete/{id}', [AccidentsAndFatalitiesByInjuryController::class, 'destroy']);


    // Accidents And Fatalities By Injury Locations

    Route::get('/accidents-and-fatalities-by-injury-locations', [AccidentsAndFatalitiesByInjuryLocationController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-injury-locations-user', [AccidentsAndFatalitiesByInjuryLocationController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-injury-locations/store', [AccidentsAndFatalitiesByInjuryLocationController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-injury-locations/import', [AccidentsAndFatalitiesByInjuryLocationController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-injury-locations/update/{id}', [AccidentsAndFatalitiesByInjuryLocationController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-injury-locations/delete/{id}', [AccidentsAndFatalitiesByInjuryLocationController::class, 'destroy']);


    // Accidents And Fatalities By Injury Causes

    Route::get('/accidents-and-fatalities-by-injury-causes', [AccidentsAndFatalitiesByInjuryCauseController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-injury-causes-user', [AccidentsAndFatalitiesByInjuryCauseController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-injury-causes/store', [AccidentsAndFatalitiesByInjuryCauseController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-injury-causes/import', [AccidentsAndFatalitiesByInjuryCauseController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-injury-causes/update/{id}', [AccidentsAndFatalitiesByInjuryCauseController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-injury-causes/delete/{id}', [AccidentsAndFatalitiesByInjuryCauseController::class, 'destroy']);


    // Accidents And Fatalities By General Activities

    Route::get('/accidents-and-fatalities-by-general-activities', [AccidentsAndFatalitiesByGeneralActivityController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-general-activities-user', [AccidentsAndFatalitiesByGeneralActivityController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-general-activities/store', [AccidentsAndFatalitiesByGeneralActivityController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-general-activities/import', [AccidentsAndFatalitiesByGeneralActivityController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-general-activities/update/{id}', [AccidentsAndFatalitiesByGeneralActivityController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-general-activities/delete/{id}', [AccidentsAndFatalitiesByGeneralActivityController::class, 'destroy']);


    // Accidents And Fatalities By Special Activities

    Route::get('/accidents-and-fatalities-by-special-activities', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-special-activities-user', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-special-activities/store', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-special-activities/import', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-special-activities/update/{id}', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-special-activities/delete/{id}', [AccidentsAndFatalitiesBySpecialActivityBeforeAccidentController::class, 'destroy']);


    // Accidents And Fatalities By Deviations

    Route::get('/accidents-and-fatalities-by-deviations', [AccidentsAndFatalitiesByDeviationController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-deviations-user', [AccidentsAndFatalitiesByDeviationController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-deviations/store', [AccidentsAndFatalitiesByDeviationController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-deviations/import', [AccidentsAndFatalitiesByDeviationController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-deviations/update/{id}', [AccidentsAndFatalitiesByDeviationController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-deviations/delete/{id}', [AccidentsAndFatalitiesByDeviationController::class, 'destroy']);


    // Accidents And Fatalities By Materials

    Route::get('/accidents-and-fatalities-by-materials', [AccidentsAndFatalitiesByMaterialController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-materials-user', [AccidentsAndFatalitiesByMaterialController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-materials/store', [AccidentsAndFatalitiesByMaterialController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-materials/import', [AccidentsAndFatalitiesByMaterialController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-materials/update/{id}', [AccidentsAndFatalitiesByMaterialController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-materials/delete/{id}', [AccidentsAndFatalitiesByMaterialController::class, 'destroy']);


    // Accidents And Fatalities By Environments

    Route::get('/accidents-and-fatalities-by-environments', [AccidentsAndFatalitiesByEnvironmentController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-environments-user', [AccidentsAndFatalitiesByEnvironmentController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-environments/store', [AccidentsAndFatalitiesByEnvironmentController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-environments/import', [AccidentsAndFatalitiesByEnvironmentController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-environments/update/{id}', [AccidentsAndFatalitiesByEnvironmentController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-environments/delete/{id}', [AccidentsAndFatalitiesByEnvironmentController::class, 'destroy']);


    // Work Accidents By Workstation

    Route::get('/work-accidents-by-work-stations', [WorkAccidentByWorkstationController::class, 'index']);
    Route::get('/work-accidents-by-work-stations-user', [WorkAccidentByWorkstationController::class, 'indexUser']);
    Route::post('/work-accidents-by-work-stations/store', [WorkAccidentByWorkstationController::class, 'store']);
    Route::post('/work-accidents-by-work-stations/import', [WorkAccidentByWorkstationController::class, 'import']);
    Route::put('/work-accidents-by-work-stations/update/{id}', [WorkAccidentByWorkstationController::class, 'update']);
    Route::delete('/work-accidents-by-work-stations/delete/{id}', [WorkAccidentByWorkstationController::class, 'destroy']);


    // Work Accidents By Hours

    Route::get('/work-accidents-by-hours', [WorkAccidentByHourController::class, 'index']);
    Route::get('/work-accidents-by-hours-user', [WorkAccidentByHourController::class, 'indexUser']);
    Route::post('/work-accidents-by-hours/store', [WorkAccidentByHourController::class, 'store']);
    Route::post('/work-accidents-by-hours/import', [WorkAccidentByHourController::class, 'import']);
    Route::put('/work-accidents-by-hours/update/{id}', [WorkAccidentByHourController::class, 'update']);
    Route::delete('/work-accidents-by-hours/delete/{id}', [WorkAccidentByHourController::class, 'destroy']);


    // Work Accidents By Employee

    Route::get('/accidents-and-fatalities-by-employee', [AccidentsAndFatalitiesByEmployeeController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-employee-user', [AccidentsAndFatalitiesByEmployeeController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-employee/store', [AccidentsAndFatalitiesByEmployeeController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-employee/import', [AccidentsAndFatalitiesByEmployeeController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-employee/update/{id}', [AccidentsAndFatalitiesByEmployeeController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-employee/delete/{id}', [AccidentsAndFatalitiesByEmployeeController::class, 'destroy']);


    // Occ Disease Fatalities By Employee

    Route::get('/occ-disease-fatalities-by-employee', [OccDiseaseFatalitiesByEmployeeController::class, 'index']);
    Route::get('/occ-disease-fatalities-by-employee-user', [OccDiseaseFatalitiesByEmployeeController::class, 'indexUser']);
    Route::post('/occ-disease-fatalities-by-employee/store', [OccDiseaseFatalitiesByEmployeeController::class, 'store']);
    Route::post('/occ-disease-fatalities-by-employee/import', [OccDiseaseFatalitiesByEmployeeController::class, 'import']);
    Route::put('/occ-disease-fatalities-by-employee/update/{id}', [OccDiseaseFatalitiesByEmployeeController::class, 'update']);
    Route::delete('/occ-disease-fatalities-by-employee/delete/{id}', [OccDiseaseFatalitiesByEmployeeController::class, 'destroy']);


    // Occ Disease Fatalities By Employer Durations

    Route::get('/occ-disease-fatalities-by-employer-durations', [OccDiseaseFatalitiesByEmployerDurationController::class, 'index']);
    Route::get('/occ-disease-fatalities-by-employer-durations-user', [OccDiseaseFatalitiesByEmployerDurationController::class, 'indexUser']);
    Route::post('/occ-disease-fatalities-by-employer-durations/store', [OccDiseaseFatalitiesByEmployerDurationController::class, 'store']);
    Route::post('/occ-disease-fatalities-by-employer-durations/import', [OccDiseaseFatalitiesByEmployerDurationController::class, 'import']);
    Route::put('/occ-disease-fatalities-by-employer-durations/update/{id}', [OccDiseaseFatalitiesByEmployerDurationController::class, 'update']);
    Route::delete('/occ-disease-fatalities-by-employer-durations/delete/{id}', [OccDiseaseFatalitiesByEmployerDurationController::class, 'destroy']);


    // Accidents And Fatalities By Employer Durations

    Route::get('/accidents-and-fatalities-by-employer-durations', [AccidentsAndFatalitiesByEmployerDurationController::class, 'index']);
    Route::get('/accidents-and-fatalities-by-employer-durations-user', [AccidentsAndFatalitiesByEmployerDurationController::class, 'indexUser']);
    Route::post('/accidents-and-fatalities-by-employer-durations/store', [AccidentsAndFatalitiesByEmployerDurationController::class, 'store']);
    Route::post('/accidents-and-fatalities-by-employer-durations/import', [AccidentsAndFatalitiesByEmployerDurationController::class, 'import']);
    Route::put('/accidents-and-fatalities-by-employer-durations/update/{id}', [AccidentsAndFatalitiesByEmployerDurationController::class, 'update']);
    Route::delete('/accidents-and-fatalities-by-employer-durations/delete/{id}', [AccidentsAndFatalitiesByEmployerDurationController::class, 'destroy']);





