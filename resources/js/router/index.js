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
    console.log(`Navigation: ${from.path} → ${to.path}`);
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

    console.log("Auth state:", {
        isAuthenticated,
        hasUser: !!user,
        hasToken: !!authStore.token,
        userRoles: user?.roles,
    });

    // If trying to access login page while authenticated, redirect to dashboard
    if (to.path === "/login" && isAuthenticated) {
        console.log(
            "Redirecting from login to dashboard, isAuthenticated:",
            isAuthenticated
        );
        next("/");
        return;
    }

    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        console.log(
            "Route requires auth but user not authenticated, redirecting to login"
        );
        next("/login");
        return;
    }

    // Check if route requires admin privileges
    if (to.meta.requiresAdmin && !isAuthenticated) {
        console.log(
            "Route requires admin but user not authenticated, redirecting to login"
        );
        next("/login");
        return;
    }

    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && isAuthenticated) {
        console.log(
            "Route requires guest but user is authenticated, redirecting to dashboard"
        );
        next("/");
        return;
    }

    console.log("Navigation allowed, proceeding to:", to.path);
    next();
});

export default router;
