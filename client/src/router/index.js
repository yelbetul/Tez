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
      path: '/admin/developers',
      name: 'admin_developers',
      component: () => import('../views/panel/DevelopersView.vue'),
    },
  ],
})

export default router
