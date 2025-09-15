<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Loading state -->
        <div
            v-if="isAuthLoading"
            class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100"
        >
            <div class="text-center">
                <div
                    class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent mx-auto mb-4"
                ></div>
                <p class="text-gray-600 font-medium">Đang tải hệ thống...</p>
            </div>
        </div>

        <!-- Main Admin Layout -->
        <div v-else-if="isAuthenticated" class="flex h-screen bg-gray-50">
            <!-- Sidebar -->
            <div
                :class="[
                    'fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                ]"
            >
                <div class="flex flex-col h-full">
                    <!-- Logo & Brand -->
                    <div
                        class="flex items-center justify-between h-20 px-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-white rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-white">
                                    TechStore
                                </h1>
                                <p class="text-blue-100 text-sm">Admin Panel</p>
                            </div>
                        </div>
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden p-2 rounded-lg text-white hover:bg-white/20 transition-colors"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                        <!-- Dashboard -->
                        <router-link
                            to="/"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path === '/'
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('dashboard')"
                        >
                            <ChartBarIcon class="w-5 h-5 mr-3" />
                            <span>Tổng quan</span>
                            <div
                                v-if="$route.path === '/'"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Products -->
                        <router-link
                            to="/products"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/products')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('products')"
                        >
                            <DevicePhoneMobileIcon class="w-5 h-5 mr-3" />
                            <span>Sản phẩm</span>
                            <div
                                v-if="$route.path.startsWith('/products')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Categories -->
                        <router-link
                            to="/categories"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/categories')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('categories')"
                        >
                            <TagIcon class="w-5 h-5 mr-3" />
                            <span>Danh mục</span>
                            <div
                                v-if="$route.path.startsWith('/categories')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Orders -->
                        <router-link
                            to="/orders"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/orders')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('orders')"
                        >
                            <ShoppingBagIcon class="w-5 h-5 mr-3" />
                            <span>Đơn hàng</span>
                            <div
                                v-if="$route.path.startsWith('/orders')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Customers -->
                        <router-link
                            to="/customers"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/customers')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('customers')"
                        >
                            <UsersIcon class="w-5 h-5 mr-3" />
                            <span>Khách hàng</span>
                            <div
                                v-if="$route.path.startsWith('/customers')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Analytics -->
                        <router-link
                            to="/analytics"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/analytics')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('analytics')"
                        >
                            <ChartPieIcon class="w-5 h-5 mr-3" />
                            <span>Phân tích</span>
                            <div
                                v-if="$route.path.startsWith('/analytics')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Activity Logs -->

                        <router-link
                            to="/activity-logs"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/activity-logs')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('activity-logs')"
                        >
                            <ComputerDesktopIcon class="w-5 h-5 mr-3" />
                            <span>Hoạt động</span>

                            <div
                                v-if="$route.path.startsWith('/activity-logs')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-4"></div>

                        <!-- Settings -->
                        <router-link
                            to="/settings"
                            class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group"
                            :class="[
                                $route.path.startsWith('/settings')
                                    ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                            ]"
                            @click="handleNavigation('settings')"
                        >
                            <Cog6ToothIcon class="w-5 h-5 mr-3" />
                            <span>Cài đặt</span>
                            <div
                                v-if="$route.path.startsWith('/settings')"
                                class="ml-auto w-2 h-2 bg-blue-600 rounded-full"
                            ></div>
                        </router-link>
                    </nav>

                    <!-- User Profile Section -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold"
                            >
                                {{
                                    user?.name?.charAt(0)?.toUpperCase() || "A"
                                }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 truncate"
                                >
                                    {{ user?.name || "Admin" }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ user?.email || "admin@techstore.com" }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Header -->
                <header
                    class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40"
                >
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <!-- Left side -->
                            <div class="flex items-center space-x-4">
                                <button
                                    @click="toggleSidebar"
                                    class="p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 lg:hidden transition-colors"
                                >
                                    <Bars3Icon class="h-6 w-6" />
                                </button>

                                <!-- Breadcrumb -->
                                <nav
                                    class="hidden md:flex items-center space-x-2 text-sm"
                                >
                                    <span class="text-gray-500">Trang chủ</span>
                                    <ChevronRightIcon
                                        class="w-4 h-4 text-gray-400"
                                    />
                                    <span class="text-gray-900 font-medium">{{
                                        getCurrentPageTitle()
                                    }}</span>
                                </nav>
                            </div>

                            <!-- Right side -->
                            <div class="flex items-center space-x-4">
                                <!-- Search -->
                                <div class="hidden md:block relative">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                    >
                                        <MagnifyingGlassIcon
                                            class="h-5 w-5 text-gray-400"
                                        />
                                    </div>
                                    <input
                                        type="text"
                                        placeholder="Tìm kiếm..."
                                        class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <!-- Notifications -->
                                <button
                                    class="relative p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <BellIcon class="h-6 w-6" />
                                    <span
                                        class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500"
                                    ></span>
                                </button>

                                <!-- User Menu -->
                                <div class="relative" ref="userMenuRef">
                                    <button
                                        @click="toggleUserMenu"
                                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                    >
                                        <div
                                            class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold"
                                        >
                                            {{
                                                user?.name
                                                    ?.charAt(0)
                                                    ?.toUpperCase() || "A"
                                            }}
                                        </div>
                                        <div class="hidden md:block text-left">
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ user?.name || "Admin" }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Quản trị viên
                                            </p>
                                        </div>
                                        <ChevronDownIcon
                                            class="h-4 w-4 text-gray-400"
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
                                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200"
                                        >
                                            <div
                                                class="px-4 py-3 border-b border-gray-100"
                                            >
                                                <p
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ user?.name || "Admin" }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        user?.email ||
                                                        "admin@techstore.com"
                                                    }}
                                                </p>
                                            </div>

                                            <router-link
                                                to="/profile"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                                @click="showUserMenu = false"
                                            >
                                                <UserIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Hồ sơ cá nhân
                                            </router-link>

                                            <router-link
                                                to="/settings"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                                @click="showUserMenu = false"
                                            >
                                                <Cog6ToothIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Cài đặt
                                            </router-link>

                                            <div
                                                class="border-t border-gray-100 my-1"
                                            ></div>

                                            <button
                                                @click="logout"
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                            >
                                                <ArrowRightOnRectangleIcon
                                                    class="w-4 h-4 mr-3"
                                                />
                                                Đăng xuất
                                            </button>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 relative overflow-y-auto">
                    <div class="py-6">
                        <div class="mx-auto px-6">
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
    XMarkIcon,
    BellIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    UserIcon,
    Cog6ToothIcon,
    ArrowRightOnRectangleIcon,
    ChartBarIcon,
    DevicePhoneMobileIcon,
    TagIcon,
    ShoppingBagIcon,
    UsersIcon,
    ChartPieIcon,
    MagnifyingGlassIcon,
    ComputerDesktopIcon,
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

// Watch for auth state changes
watch(
    () => authStore.isAuthenticated,
    (newValue, oldValue) => {
        console.log("Auth state changed:", { oldValue, newValue });
    },
    { immediate: true }
);

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const handleNavigation = (routeName) => {
    console.log(`Navigating to ${routeName}`);
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

const getCurrentPageTitle = () => {
    const titles = {
        "/": "Tổng quan",
        "/products": "Sản phẩm",
        "/categories": "Danh mục",
        "/orders": "Đơn hàng",
        "/customers": "Khách hàng",
        "/analytics": "Phân tích",
        "/settings": "Cài đặt",
        "/activity-logs": "Hoạt động",
    };
    return titles[route.path] || "Trang chủ";
};

onMounted(async () => {
    document.addEventListener("click", handleClickOutside);

    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
        } catch (error) {
            authStore.logout();
        }
    }

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
