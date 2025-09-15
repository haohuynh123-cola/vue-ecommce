<template>
    <div>
        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">
                Welcome back, {{ user?.name }}! Here's what's happening with
                your store.
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Products -->
            <div class="card hover-lift">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Total Products
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ stats.totalProducts }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="card hover-lift">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Total Orders
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ stats.totalOrders }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="card hover-lift">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-yellow-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Total Revenue
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                ${{ stats.totalRevenue.toLocaleString() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="card hover-lift">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-purple-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">
                                Total Customers
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ stats.totalCustomers }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-medium text-gray-900">
                        Recent Orders
                    </h3>
                </div>
                <div class="card-body">
                    <div v-if="isLoading" class="flex justify-center py-8">
                        <div class="loading-spinner-lg"></div>
                    </div>
                    <div
                        v-else-if="recentOrders.length === 0"
                        class="text-center py-8 text-gray-500"
                    >
                        No recent orders
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="order in recentOrders"
                            :key="order.id"
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                        >
                            <div>
                                <p class="font-medium text-gray-900">
                                    Order #{{ order.origin_id }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ order.customer_name }} -
                                    {{ formatDate(order.created_at) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">
                                    ${{ order.total }}
                                </p>
                                <span
                                    :class="getStatusClass(order.status)"
                                    class="badge"
                                >
                                    {{ formatStatus(order.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-medium text-gray-900">
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <router-link
                            to="/admin/products"
                            class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200"
                        >
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4"
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
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    Manage Products
                                </p>
                                <p class="text-sm text-gray-500">
                                    Add, edit, or remove products
                                </p>
                            </div>
                        </router-link>

                        <router-link
                            to="/admin/orders"
                            class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200"
                        >
                            <div
                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4"
                            >
                                <svg
                                    class="w-6 h-6 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    View Orders
                                </p>
                                <p class="text-sm text-gray-500">
                                    Process and track orders
                                </p>
                            </div>
                        </router-link>

                        <router-link
                            to="/admin/users"
                            class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200"
                        >
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    Manage Users
                                </p>
                                <p class="text-sm text-gray-500">
                                    View and manage user accounts
                                </p>
                            </div>
                        </router-link>

                        <router-link
                            to="/admin/categories"
                            class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors duration-200"
                        >
                            <div
                                class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4"
                            >
                                <svg
                                    class="w-6 h-6 text-yellow-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">
                                    Manage Categories
                                </p>
                                <p class="text-sm text-gray-500">
                                    Organize product categories
                                </p>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "../stores/auth";
import axios from "axios";

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const stats = ref({
    totalProducts: 0,
    totalOrders: 0,
    totalRevenue: 0,
    totalCustomers: 0,
});

const recentOrders = ref([]);
const isLoading = ref(false);

const fetchStats = async () => {
    try {
        const [productsResponse, ordersResponse, usersResponse] =
            await Promise.all([
                axios.get("/api/products?per_page=1"),
                axios.get("/api/orders?per_page=1"),
                axios.get("/api/users?per_page=1"),
            ]);

        stats.value = {
            totalProducts: productsResponse.data.data.total || 0,
            totalOrders: ordersResponse.data.data.total || 0,
            totalRevenue: 125000, // Mock data - you can calculate from orders
            totalCustomers: usersResponse.data.data.total || 0,
        };
    } catch (error) {
        console.error("Error fetching stats:", error);
    }
};

const fetchRecentOrders = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get("/api/orders?per_page=5");
        if (response.data.success) {
            recentOrders.value = response.data.data;
        } else {
            console.error("API Error:", response.data.message);
            recentOrders.value = [];
        }
    } catch (error) {
        console.error("Error fetching recent orders:", error);
        recentOrders.value = [];
    } finally {
        isLoading.value = false;
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const getStatusClass = (status) => {
    const classes = {
        waiting: "badge-warning",
        processing: "badge-primary",
        shipped: "badge-primary",
        delivered: "badge-success",
        cancelled: "badge-danger",
    };
    return classes[status] || "badge-gray";
};

onMounted(() => {
    fetchStats();
    fetchRecentOrders();
});
</script>
