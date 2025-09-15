<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Show loading state -->
        <div
            v-if="isAuthLoading"
            class="flex items-center justify-center min-h-screen"
        >
            <div
                class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"
            ></div>
        </div>

        <!-- Show admin layout if authenticated -->
        <div v-else-if="isAuthenticated" class="flex h-screen">
            <!-- Sidebar -->
            <div
                :class="[
                    'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                ]"
            >
                <div class="flex flex-col h-full">
                    <!-- Sidebar header -->
                    <div
                        class="flex items-center justify-between h-16 px-4 border-b border-gray-200"
                    >
                        <h2 class="text-lg font-semibold text-gray-900">
                            Admin Menu
                        </h2>
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                ></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-4 py-4 space-y-2">
                        <router-link
                            to="/"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('dashboard')"
                        >
                            <Squares2X2Icon class="w-5 h-5 mr-3" />
                            Dashboard
                        </router-link>

                        <router-link
                            to="/products"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('products')"
                        >
                            <CubeIcon class="w-5 h-5 mr-3" />
                            Products
                        </router-link>

                        <router-link
                            to="/categories"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('categories')"
                        >
                            <TagIcon class="w-5 h-5 mr-3" />
                            Categories
                        </router-link>

                        <router-link
                            to="/orders"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('orders')"
                        >
                            <ShoppingBagIcon class="w-5 h-5 mr-3" />
                            Orders
                        </router-link>

                        <router-link
                            to="/users"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('users')"
                        >
                            <UsersIcon class="w-5 h-5 mr-3" />
                            Users
                        </router-link>

                        <router-link
                            to="/settings"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            active-class="bg-blue-100 text-blue-700"
                            @click="handleNavigation('settings')"
                        >
                            <Cog6ToothIcon class="w-5 h-5 mr-3" />
                            Settings
                        </router-link>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col min-w-0 h-screen">
                <!-- Admin Header -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <!-- Left side -->
                            <div class="flex items-center">
                                <button
                                    @click="toggleSidebar"
                                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 lg:hidden"
                                >
                                    <Bars3Icon class="h-6 w-6" />
                                </button>

                                <div class="flex items-center ml-4">
                                    <div class="flex-shrink-0">
                                        <h1
                                            class="text-xl font-bold text-gray-900"
                                        >
                                            Admin Panel
                                        </h1>
                                    </div>
                                </div>
                            </div>

                            <!-- Right side -->
                            <div class="flex items-center space-x-4">
                                <!-- Notifications -->
                                <button
                                    class="p-2 text-gray-400 hover:text-gray-500 relative"
                                >
                                    <BellIcon class="h-6 w-6" />
                                    <span
                                        class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"
                                    ></span>
                                </button>

                                <!-- User menu -->
                                <div class="relative" ref="userMenuRef">
                                    <button
                                        @click="toggleUserMenu"
                                        class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <div
                                            class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                        >
                                            {{
                                                user?.name
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                            }}
                                        </div>
                                        <span
                                            class="ml-2 text-gray-700 font-medium hidden sm:block"
                                            >{{ user?.name }}</span
                                        >
                                        <ChevronDownIcon
                                            class="ml-1 h-4 w-4 text-gray-400 hidden sm:block"
                                        />
                                    </button>

                                    <!-- Dropdown menu -->
                                    <transition
                                        enter-active-class="transition ease-out duration-200"
                                        enter-from-class="transform opacity-0 scale-95"
                                        enter-to-class="transform opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-75"
                                        leave-from-class="transform opacity-100 scale-100"
                                        leave-to-class="transform opacity-0 scale-95"
                                    >
                                        <div
                                            v-if="showUserMenu"
                                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                                        >
                                            <router-link
                                                to="/profile"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                @click="showUserMenu = false"
                                            >
                                                <UserIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Profile
                                            </router-link>
                                            <router-link
                                                to="/settings"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                @click="showUserMenu = false"
                                            >
                                                <Cog6ToothIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Settings
                                            </router-link>
                                            <button
                                                @click="logout"
                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            >
                                                <ArrowRightOnRectangleIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Logout
                                            </button>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page content -->
                <main
                    class="flex-1 relative overflow-y-auto focus:outline-none h-full"
                >
                    <div class="py-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <router-view />
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            @click="toggleSidebar"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        ></div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "../stores/auth";
import {
    Bars3Icon,
    BellIcon,
    ChevronDownIcon,
    UserIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon,
    Squares2X2Icon,
    CubeIcon,
    TagIcon,
    ShoppingBagIcon,
    UsersIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const sidebarOpen = ref(false);
const showUserMenu = ref(false);
const userMenuRef = ref(null);

const user = computed(() => authStore.user);
const isAuthenticated = computed(() => authStore.isAuthenticated);
const isAuthLoading = ref(true);

// Watch for auth state changes (removed redirect to avoid infinite loop)
watch(
    () => authStore.isAuthenticated,
    (newValue, oldValue) => {
        console.log("Auth state changed:", { oldValue, newValue });
        // Router guard handles redirects, no need to redirect here
    },
    { immediate: true }
);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const handleNavigation = (routeName) => {
    console.log(`Navigating to ${routeName}`);
    // Close sidebar on mobile after navigation
    if (window.innerWidth < 1024) {
        sidebarOpen.value = false;
    }
};

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

const logout = async () => {
    await authStore.logout();
    router.push("/login");
};

const handleClickOutside = (event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
        showUserMenu.value = false;
    }
};

onMounted(async () => {
    document.addEventListener("click", handleClickOutside);

    // If we have a token but no user, try to fetch user
    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
        } catch (error) {
            // If fetch fails, clear auth data
            authStore.logout();
        }
    }

    // Set loading to false after auth check
    isAuthLoading.value = false;
    console.log(
        "Auth loading completed, isAuthenticated:",
        authStore.isAuthenticated
    );
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
