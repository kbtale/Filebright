import { createRouter, createWebHistory } from 'vue-router'
import { authStore } from '../stores/authStore'
import DashboardView from '../views/DashboardView.vue'
import AuthView from '../views/AuthView.vue'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/auth',
    name: 'auth',
    component: AuthView,
    meta: { guestOnly: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/auth')
  } else if (to.meta.guestOnly && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
