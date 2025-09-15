import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/login",
            component: () => import("../components/Login.vue"),
            name: "login",
            meta: {
                requiresGuest: true,
            },
        },
        {
            path: "/",
            component: () => import("../layouts/ModernAdminLayout.vue"),
            name: "dashboard",
            meta: {
                requiresAuth: true,
                requiresAdmin: true,
            },
            children: [
                {
                    path: "",
                    component: () =>
                        import("../components/ModernDashboard.vue"),
                    name: "dashboard-home",
                },
                {
                    path: "products",
                    component: () => import("../components/ModernProducts.vue"),
                    name: "products",
                },
                {
                    path: "categories",
                    component: () => import("../components/Categories.vue"),
                    name: "categories",
                },
                {
                    path: "orders",
                    component: () => import("../components/Orders.vue"),
                    name: "orders",
                },
                {
                    path: "customers",
                    component: () => import("../components/Users.vue"),
                    name: "customers",
                },
                {
                    path: "analytics",
                    component: () => import("../components/Analytics.vue"),
                    name: "analytics",
                },
                {
                    path: "activity-logs",
                    component: () => import("../components/ActivityLogs.vue"),
                    name: "activity-logs",
                },
                {
                    path: "settings",
                    component: () => import("../components/Settings.vue"),
                    name: "settings",
                },
            ],
        },
    ],
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // If we have a token but no user, try to fetch user
    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
        } catch (error) {
            // If fetch fails, clear auth data
            authStore.logout();
        }
    }

    // Check if user is authenticated
    const isAuthenticated = authStore.isAuthenticated;
    const user = authStore.user;

    // If trying to access login page while authenticated, redirect to dashboard
    if (to.path === "/login" && isAuthenticated) {
        next("/");
        return;
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
        return;
    }

    // Check if route requires admin privileges
    if (to.meta.requiresAdmin && !isAuthenticated) {
        next("/login");
        return;
    }

    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && isAuthenticated) {
        next("/");
        return;
    }

    next();
});

export default router;
