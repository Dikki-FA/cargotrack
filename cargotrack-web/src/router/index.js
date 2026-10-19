import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore} from '@/stores/auth'

const routes = [
    {
        path: '/',
        redirect: '/tracking',
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/Login.vue'),
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/Dashboard.vue'),
    },
    {
        path: '/tracking',
        name: 'tracking',
        component: () => import('@/views/PublicTracking.vue'),
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    const authStore = useAuthStore()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return { name: 'login'}
    }

    if (to.meta.roles && !to.meta.roles.includes(authStore.role)) {
        return { name: 'tracking'}
    }

    if (to.name === 'login' && authStore.isAuthenticated) {
        return { name: 'dashboard'}
    }

    return true
})
export default router