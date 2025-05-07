import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/admin/login',
      name: 'admin_login',
      component: () => import('../views/panel/LoginView.vue'),
    },
    {
      path: '/admin/welcome',
      name: 'admin_welcome',
      component: () => import('../views/panel/WelcomeView.vue'),
    },
    {
      path: '/admin/groups',
      name: 'admin_groups',
      component: () => import('../views/panel/groups/WelcomeView.vue'),
    },
    {
      path: '/admin/groups/sector-codes',
      name: 'admin_sector_codes',
      component: () => import('../views/panel/groups/SectorCodesView.vue'),
    },
    {
      path: '/admin/groups/occupation-groups',
      name: 'admin_occupation-groups',
      component: () => import('../views/panel/groups/OccupationGroupsView.vue'),
    },
    {
      path: '/admin/groups/province-codes',
      name: 'admin_province-codes',
      component: () => import('../views/panel/groups/ProvinceCodesView.vue'),
    },
    {
      path: '/admin/groups/months',
      name: 'admin_months',
      component: () => import('../views/panel/groups/MonthsView.vue'),
    },
    {
      path: '/admin/groups/diagnosis',
      name: 'admin_diagnosis',
      component: () => import('../views/panel/groups/DiagnosisView.vue'),
    },
    {
      path: '/admin/groups/ages',
      name: 'admin_ages',
      component: () => import('../views/panel/groups/AgesView.vue'),
    },
    {
      path: '/admin/groups/injury-types',
      name: 'admin_injury-types',
      component: () => import('../views/panel/groups/InjuryTypesView.vue'),
    },
    {
      path: '/admin/groups/injury-locations',
      name: 'admin_injury-locations',
      component: () => import('../views/panel/groups/InjuryLocationsView.vue'),
    },
    {
      path: '/admin/groups/injury-causes',
      name: 'admin_injury-causes',
      component: () => import('../views/panel/groups/InjuryCausesView.vue'),
    },
    {
      path: '/admin/groups/general-activities',
      name: 'admin_general-activities',
      component: () => import('../views/panel/groups/GeneralActivitiesView.vue'),
    },
    {
      path: '/admin/groups/special-activities',
      name: 'admin_special-activities',
      component: () => import('../views/panel/groups/SpecialActivitiesView.vue'),
    },
    {
      path: '/admin/groups/deviations',
      name: 'admin_deviations',
      component: () => import('../views/panel/groups/DeviationsView.vue'),
    },
    {
      path: '/admin/groups/materials',
      name: 'admin_materials',
      component: () => import('../views/panel/groups/MaterialsView.vue'),
    },
    {
      path: '/admin/groups/work-environments',
      name: 'admin_work-environments',
      component: () => import('../views/panel/groups/WorkEnvironmentsView.vue'),
    },
    {
      path: '/admin/groups/work-stations',
      name: 'admin_work-stations',
      component: () => import('../views/panel/groups/WorkStationsView.vue'),
    },
    {
      path: '/admin/groups/work-times',
      name: 'admin_work-times',
      component: () => import('../views/panel/groups/WorkTimesView.vue'),
    },
    {
      path: '/admin/groups/employee-groups',
      name: 'admin_employee-groups',
      component: () => import('../views/panel/groups/EmployeeGroupsView.vue'),
    },
    {
      path: '/admin/groups/employee-times',
      name: 'admin_employee-times',
      component: () => import('../views/panel/groups/EmployeeTimesView.vue'),
    },



    {
      path: '/admin/tables',
      name: 'admin_tables',
      component: () => import('../views/panel/tables/WelcomeView.vue'),
    },
    {
      path: '/admin/tables/sector-codes/work-accidents',
      name: 'admin_tables_sector_codes_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsBySectorCodesView.vue'),
    },
    {
      path: '/admin/tables/sector-codes/fatal-work-accidents',
      name: 'admin_tables_sector_codes_fatal_work_accidents',
      component: () => import('../views/panel/tables/FatalWorkAccidentsBySectorCodesView.vue'),
    },
    {
      path: '/admin/tables/sector-codes/temporary-disability-days',
      name: 'admin_tables_sector_codes_temporary_disability_days',
      component: () => import('../views/panel/tables/TemporaryDisabilityDaysBySectorCodesView.vue'),
    },
    {
      path: '/admin/tables/provinces/work-accidents',
      name: 'admin_tables_provinces_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByProvincesView.vue'),
    },
    {
      path: '/admin/tables/provinces/fatal-work-accidents',
      name: 'admin_tables_provinces_fatal_work_accidents',
      component: () => import('../views/panel/tables/FatalWorkAccidentsByProvincesView.vue'),
    },
    {
      path: '/admin/tables/provinces/temporary-disability-days',
      name: 'admin_tables_provinces_temporary_disability_days',
      component: () => import('../views/panel/tables/TemporaryDisabilityDaysByProvincesView.vue'),
    },
    {
      path: '/admin/tables/provinces/disability-days',
      name: 'admin_tables_provinces_disability_days',
      component: () => import('../views/panel/tables/DisabilityDaysOccupationalDiseasesByProvinceView.vue'),
    },
    {
      path: '/admin/tables/ages/work-accidents',
      name: 'admin_tables_ages_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByAgesView.vue'),
    },
    {
      path: '/admin/tables/ages/fatal-work-accidents',
      name: 'admin_tables_ages_fatal_work_accidents',
      component: () => import('../views/panel/tables/FatalWorkAccidentsByAgesView.vue'),
    },
    {
      path: '/admin/tables/months/temporary-disability-days',
      name: 'admin_tables_months_temporary_disability_days',
      component: () => import('../views/panel/tables/TemporaryDisabilityDaysByMonthsView.vue'),
    },
    {
      path: '/admin/tables/months/work-accidents',
      name: 'admin_tables_months_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByMonthsView.vue'),
    },
    {
      path: '/admin/tables/months/fatal-work-accidents',
      name: 'admin_tables_months_fatal_work_accidents',
      component: () => import('../views/panel/tables/FatalWorkAccidentsByMonthsView.vue'),
    },
    {
      path: '/admin/tables/occupations/work-accidents',
      name: 'admin_tables_occupations_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByOccupationsView.vue'),
    },
    {
      path: '/admin/tables/occupations/disease-fatalities',
      name: 'admin_tables_occupations_disease_fatalities',
      component: () => import('../views/panel/tables/OccDiseaseFatalitiesByOccupationsView.vue'),
    },
    {
      path: '/admin/tables/injury-types/work-accidents',
      name: 'admin_tables_injury_types_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByInjuryTypesView.vue'),
    },
    {
      path: '/admin/tables/injury-locations/work-accidents',
      name: 'admin_tables_injury_locations_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByInjuryLocationsView.vue'),
    },
    {
      path: '/admin/tables/injury-causes/work-accidents',
      name: 'admin_tables_injury_causes_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByInjuryCausesView.vue'),
    },
    {
      path: '/admin/tables/general-activities/work-accidents',
      name: 'admin_tables_general_activities_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByGeneralActivitiesView.vue'),
    },
    {
      path: '/admin/tables/special-activities/work-accidents',
      name: 'admin_tables_special_activities_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsBySpecialActivitiesView.vue'),
    },
    {
      path: '/admin/tables/deviations/work-accidents',
      name: 'admin_tables_deviations_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByDeviationsView.vue'),
    },
    {
      path: '/admin/tables/materials/work-accidents',
      name: 'admin_tables_materials_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByMaterialsView.vue'),
    },
    {
      path: '/admin/tables/environments/work-accidents',
      name: 'admin_tables_environments_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByEnvironmentsView.vue'),
    },
    {
      path: '/admin/tables/diagnosis-groups/occupational-diasese',
      name: 'admin_tables_diagnosis_groups_occupational_diasese',
      component: () => import('../views/panel/tables/OccupationalDiseaseByDiagnosisView.vue'),
    },
    {
      path: '/admin/tables/work-stations/work-accidents',
      name: 'admin_tables_work_stations_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByWorkStationsView.vue'),
    },
    {
      path: '/admin/tables/time-intervals/work-accidents',
      name: 'admin_tables_time_intervals_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByHoursView.vue'),
    },
    {
      path: '/admin/tables/employee/work-accidents',
      name: 'admin_tables_employee_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByEmployeesView.vue'),
    },
    {
      path: '/admin/tables/employee/disease-fatalities',
      name: 'admin_tables_employees_disease_fatalities',
      component: () => import('../views/panel/tables/OccDiseaseFatalitiesByEmployeesView.vue'),
    },
    {
      path: '/admin/tables/employer-durations/disease-fatalities',
      name: 'admin_tables_employer_durations_disease_fatalities',
      component: () => import('../views/panel/tables/OccDiseaseFatalitiesByEmployerDurationsView.vue'),
    },
    {
      path: '/admin/tables/employer-durations/work-accidents',
      name: 'admin_tables_employer_durations_work_accidents',
      component: () => import('../views/panel/tables/WorkAccidentsByEmployerDurationsView.vue'),
    },

    {
      path: '/admin/developers',
      name: 'admin_developers',
      component: () => import('../views/panel/DevelopersView.vue'),
    },



    {
      path: '/analysis/3.1.1',
      name: 'sector_accidents',
      component: () => import('../views/user/SectorAccidents.vue'),
    },
    {
      path: '/analysis/3.1.2',
      name: 'sector_fatal_accidents',
      component: () => import('../views/user/SectorFatalAccidents.vue'),
    },
    {
      path: '/analysis/3.1.3',
      name: 'sector_temporary_disability',
      component: () => import('../views/user/SectorTemporaryDisability.vue'),
    },
    {
      path: '/analysis/3.1.4',
      name: 'province_accidents',
      component: () => import('../views/user/ProvinceAccidents.vue'),
    },
    {
      path: '/analysis/3.1.5',
      name: 'province_fatal_accidents',
      component: () => import('../views/user/ProvinceFatalAccidents.vue'),
    },
    {
      path: '/analysis/3.1.6',
      name: 'province_temporary_disability',
      component: () => import('../views/user/ProvinceTemporaryDisability.vue'),
    },
    {
      path: '/analysis/3.1.7',
      name: 'province_disability_days',
      component: () => import('../views/user/ProvinceDisabilityDays.vue'),
    },
    {
      path: '/analysis/3.1.8',
      name: 'age_accidents',
      component: () => import('../views/user/AgeAccidents.vue'),
    },
    {
      path: '/analysis/3.1.9',
      name: 'age_fatal_accidents',
      component: () => import('../views/user/AgeFatalAccidents.vue'),
    },
    {
      path: '/analysis/3.1.10',
      name: 'occupational_disease',
      component: () => import('../views/user/OccupationalDisease.vue'),
    },
    {
      path: '/analysis/3.1.11',
      name: 'month_accidents',
      component: () => import('../views/user/MonthAccidents.vue'),
    },
    {
      path: '/analysis/3.1.12',
      name: 'month_temporary_disability',
      component: () => import('../views/user/MonthTemporaryDisability.vue'),
    },
    {
      path: '/analysis/3.1.13',
      name: 'month_fatal_accidents',
      component: () => import('../views/user/MonthFatalAccidents.vue'),
    },
    {
      path: '/analysis/3.1.14',
      name: 'occupation_accidents',
      component: () => import('../views/user/OccupationAccidents.vue'),
    },
  ],
})

export default router
