<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Tổng quan hệ thống
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Chào mừng trở lại, {{ user?.name || "Admin" }}! Đây là
                        báo cáo tổng quan của cửa hàng.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ formatTime(new Date()) }}
                        </p>
                    </div>
                    <button
                        @click="refreshData"
                        :disabled="isLoading"
                        class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 disabled:opacity-50 transition-colors"
                    >
                        <ArrowPathIcon
                            class="h-5 w-5"
                            :class="{ 'animate-spin': isLoading }"
                        />
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Tổng doanh thu
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatCurrency(stats.totalRevenue) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+12.5%</span
                            >
                            <span class="text-sm text-gray-500 ml-1"
                                >so với tháng trước</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center"
                    >
                        <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Tổng đơn hàng
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatNumber(stats.totalOrders) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+8.2%</span
                            >
                            <span class="text-sm text-gray-500 ml-1"
                                >so với tháng trước</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center"
                    >
                        <ShoppingBagIcon class="h-6 w-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Sản phẩm
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatNumber(stats.totalProducts) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+3.1%</span
                            >
                            <span class="text-sm text-gray-500 ml-1"
                                >so với tháng trước</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"
                    >
                        <DevicePhoneMobileIcon
                            class="h-6 w-6 text-purple-600"
                        />
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Khách hàng
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatNumber(stats.totalCustomers) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+15.3%</span
                            >
                            <span class="text-sm text-gray-500 ml-1"
                                >so với tháng trước</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center"
                    >
                        <UsersIcon class="h-6 w-6 text-orange-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Revenue Chart -->
            <div
                class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Doanh thu theo tháng
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button
                            class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-lg"
                        >
                            6 tháng
                        </button>
                        <button
                            class="px-3 py-1 text-sm text-gray-500 hover:bg-gray-50 rounded-lg"
                        >
                            1 năm
                        </button>
                    </div>
                </div>
                <div
                    class="h-64 flex items-center justify-center bg-gray-50 rounded-lg"
                >
                    <div class="text-center">
                        <ChartBarIcon
                            class="h-12 w-12 text-gray-400 mx-auto mb-2"
                        />
                        <p class="text-gray-500">
                            Biểu đồ doanh thu sẽ được hiển thị ở đây
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top Categories -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Danh mục bán chạy
                </h3>
                <div class="space-y-4">
                    <div
                        v-for="(category, index) in topCategories"
                        :key="category.id"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"
                            >
                                <span
                                    class="text-sm font-semibold text-blue-600"
                                    >{{ index + 1 }}</span
                                >
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ category.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ category.products }} sản phẩm
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ formatCurrency(category.revenue) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ category.orders }} đơn
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Đơn hàng gần đây
                        </h3>
                        <router-link
                            to="/orders"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            Xem tất cả
                        </router-link>
                    </div>
                </div>
                <div class="p-6">
                    <div v-if="isLoading" class="flex justify-center py-8">
                        <div
                            class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"
                        ></div>
                    </div>
                    <div
                        v-else-if="recentOrders.length === 0"
                        class="text-center py-8 text-gray-500"
                    >
                        <ShoppingBagIcon
                            class="h-12 w-12 text-gray-300 mx-auto mb-4"
                        />
                        <p>Chưa có đơn hàng nào</p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="order in recentOrders"
                            :key="order.id"
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                                >
                                    <ShoppingBagIcon
                                        class="h-5 w-5 text-blue-600"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        #{{ order.origin_id }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ order.customer_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ formatCurrency(order.total) }}
                                </p>
                                <span
                                    :class="getStatusClass(order.status)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ formatStatus(order.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Thao tác nhanh
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <router-link
                            to="/products/create"
                            class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group"
                        >
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors"
                            >
                                <PlusIcon class="h-6 w-6 text-blue-600" />
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                Thêm sản phẩm
                            </p>
                        </router-link>

                        <router-link
                            to="/orders"
                            class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group"
                        >
                            <div
                                class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-200 transition-colors"
                            >
                                <ShoppingBagIcon
                                    class="h-6 w-6 text-green-600"
                                />
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                Xem đơn hàng
                            </p>
                        </router-link>

                        <router-link
                            to="/customers"
                            class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group"
                        >
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-colors"
                            >
                                <UsersIcon class="h-6 w-6 text-purple-600" />
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                Quản lý khách hàng
                            </p>
                        </router-link>

                        <router-link
                            to="/analytics"
                            class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors group"
                        >
                            <div
                                class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-200 transition-colors"
                            >
                                <ChartPieIcon class="h-6 w-6 text-orange-600" />
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                Báo cáo
                            </p>
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
import {
    ArrowUpIcon,
    ArrowPathIcon,
    CurrencyDollarIcon,
    ShoppingBagIcon,
    DevicePhoneMobileIcon,
    UsersIcon,
    ChartBarIcon,
    ChartPieIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";

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

const topCategories = ref([
    {
        id: 1,
        name: "Điện thoại",
        products: 156,
        revenue: 125000000,
        orders: 89,
    },
    { id: 2, name: "Laptop", products: 78, revenue: 98000000, orders: 45 },
    { id: 3, name: "Phụ kiện", products: 234, revenue: 45000000, orders: 123 },
    {
        id: 4,
        name: "Máy tính bảng",
        products: 45,
        revenue: 32000000,
        orders: 28,
    },
    {
        id: 5,
        name: "Đồng hồ thông minh",
        products: 67,
        revenue: 28000000,
        orders: 34,
    },
]);

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
            totalRevenue: 125000000, // Mock data
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

const refreshData = async () => {
    isLoading.value = true;
    await Promise.all([fetchStats(), fetchRecentOrders()]);
    isLoading.value = false;
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(amount);
};

const formatNumber = (number) => {
    return new Intl.NumberFormat("vi-VN").format(number);
};

const formatTime = (date) => {
    return new Intl.DateTimeFormat("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    }).format(date);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatStatus = (status) => {
    const statusMap = {
        waiting: "Chờ xử lý",
        processing: "Đang xử lý",
        shipped: "Đã giao",
        delivered: "Hoàn thành",
        cancelled: "Đã hủy",
    };
    return statusMap[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        waiting: "bg-yellow-100 text-yellow-800",
        processing: "bg-blue-100 text-blue-800",
        shipped: "bg-blue-100 text-blue-800",
        delivered: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

onMounted(() => {
    fetchStats();
    fetchRecentOrders();
});
</script>
