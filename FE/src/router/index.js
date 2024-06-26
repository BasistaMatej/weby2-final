import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/registration',
      name: 'registration',
      component: () => import('../views/Registration.vue')
    },
    {
      path: '/activate/:token',
      name: 'Activate email',
      component: () => import('../views/EmailValidationView.vue')
    },
    {
      path: '/auth',
      name: 'Welcome!',
      component: () => import('../views/AuthHomeView.vue')
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('../views/ProfileView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Login.vue')
    },
    {
      path: '/users',
      name: 'users',
      component: () => import('../views/Users.vue')
    },
    {
      path: '/manual',
      name: 'User Manual',
      component: () => import('../views/UserManualView.vue')
    },
    {
      path: '/reset/:token',
      name: 'Reset password',
      component: () => import('../views/ResetPasswordView.vue')
    },
    {
      path: '/history/:id/:type',
      name: 'History',
      component: () => import('../views/HistoryView.vue')
    },
    {
      path: '/:code',
      name: 'Question',
      component: () => import('../views/LiveQuestionView.vue')
    },
  ]
})

export default router
