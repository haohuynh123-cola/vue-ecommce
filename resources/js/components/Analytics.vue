<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Phân tích & Báo cáo
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Theo dõi hiệu suất kinh doanh và xu hướng bán hàng
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <select
                        v-model="selectedPeriod"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="7">7 ngày qua</option>
                        <option value="30">30 ngày qua</option>
                        <option value="90">3 tháng qua</option>
                        <option value="365">1 năm qua</option>
                    </select>
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

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Tổng doanh thu
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatCurrency(metrics.totalRevenue) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+{{ metrics.revenueGrowth }}%</span
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

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Đơn hàng
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatNumber(metrics.totalOrders) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+{{ metrics.ordersGrowth }}%</span
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

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Khách hàng mới
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ formatNumber(metrics.newCustomers) }}
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+{{ metrics.customersGrowth }}%</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"
                    >
                        <UsersIcon class="h-6 w-6 text-purple-600" />
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">
                            Tỷ lệ chuyển đổi
                        </p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ metrics.conversionRate }}%
                        </p>
                        <div class="flex items-center mt-2">
                            <ArrowUpIcon class="h-4 w-4 text-green-500" />
                            <span
                                class="text-sm text-green-600 font-medium ml-1"
                                >+{{ metrics.conversionGrowth }}%</span
                            >
                        </div>
                    </div>
                    <div
                        class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center"
                    >
                        <ChartBarIcon class="h-6 w-6 text-orange-600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Chart -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Doanh thu theo thời gian
                </h3>
                <div
                    class="h-64 flex items-center justify-center bg-gray-50 rounded-lg"
                >
                    <div class="text-center">
                        <ChartBarIcon
                            class="h-12 w-12 text-gray-400 mx-auto mb-2"
                        />
                        <p class="text-gray-500">Biểu đồ doanh thu</p>
                    </div>
                </div>
            </div>

            <!-- Orders Chart -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Số lượng đơn hàng
                </h3>
                <div
                    class="h-64 flex items-center justify-center bg-gray-50 rounded-lg"
                >
                    <div class="text-center">
                        <ShoppingBagIcon
                            class="h-12 w-12 text-gray-400 mx-auto mb-2"
                        />
                        <p class="text-gray-500">Biểu đồ đơn hàng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products and Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Products -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Sản phẩm bán chạy
                </h3>
                <div class="space-y-4">
                    <div
                        v-for="(product, index) in topProducts"
                        :key="product.id"
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
                            <div
                                class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden"
                            >
                                <img
                                    :src="
                                        product.image ||
                                        '/images/placeholder-product.jpg'
                                    "
                                    :alt="product.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ product.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ product.sold }} đã bán
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ formatCurrency(product.revenue) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ product.orders }} đơn
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Categories -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Danh mục phổ biến
                </h3>
                <div class="space-y-4">
                    <div
                        v-for="(category, index) in topCategories"
                        :key="category.id"
                        class="flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"
                            >
                                <span
                                    class="text-sm font-semibold text-green-600"
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

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">
                Hoạt động gần đây
            </h3>
            <div class="space-y-4">
                <div
                    v-for="activity in recentActivities"
                    :key="activity.id"
                    class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg"
                >
                    <div
                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center"
                    >
                        <component
                            :is="getActivityIcon(activity.type)"
                            class="h-5 w-5 text-blue-600"
                        />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ activity.description }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ formatTime(activity.created_at) }}
                        </p>
                    </div>
                    <span
                        :class="getActivityStatusClass(activity.status)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                    >
                        {{ getActivityStatusText(activity.status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import {
    ArrowPathIcon,
    ArrowUpIcon,
    CurrencyDollarIcon,
    ShoppingBagIcon,
    UsersIcon,
    ChartBarIcon,
    DevicePhoneMobileIcon,
    TagIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";

const selectedPeriod = ref("30");
const isLoading = ref(false);

const metrics = ref({
    totalRevenue: 125000000,
    revenueGrowth: 12.5,
    totalOrders: 1247,
    ordersGrowth: 8.2,
    newCustomers: 89,
    customersGrowth: 15.3,
    conversionRate: 3.2,
    conversionGrowth: 2.1,
});

const topProducts = ref([
    {
        id: 1,
        name: "iPhone 15 Pro Max",
        image: "/images/iphone-15-pro-max.jpg",
        sold: 156,
        revenue: 45000000,
        orders: 89,
    },
    {
        id: 2,
        name: "MacBook Air M2",
        image: "/images/macbook-air-m2.jpg",
        sold: 78,
        revenue: 32000000,
        orders: 45,
    },
    {
        id: 3,
        name: "AirPods Pro 2",
        image: "/images/airpods-pro-2.jpg",
        sold: 234,
        revenue: 18000000,
        orders: 123,
    },
    {
        id: 4,
        name: "iPad Air 5",
        image: "/images/ipad-air-5.jpg",
        sold: 45,
        revenue: 15000000,
        orders: 28,
    },
    {
        id: 5,
        name: "Apple Watch Series 9",
        image: "/images/apple-watch-9.jpg",
        sold: 67,
        revenue: 12000000,
        orders: 34,
    },
]);

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

const recentActivities = ref([
    {
        id: 1,
        type: "order",
        description: "Đơn hàng mới #ORD-001 được tạo",
        status: "success",
        created_at: new Date(),
    },
    {
        id: 2,
        type: "product",
        description: "Sản phẩm iPhone 15 Pro Max được thêm mới",
        status: "info",
        created_at: new Date(Date.now() - 1000 * 60 * 30),
    },
    {
        id: 3,
        type: "customer",
        description: "Khách hàng mới đăng ký tài khoản",
        status: "success",
        created_at: new Date(Date.now() - 1000 * 60 * 60),
    },
    {
        id: 4,
        type: "order",
        description: "Đơn hàng #ORD-002 đã được giao thành công",
        status: "success",
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 2),
    },
    {
        id: 5,
        type: "product",
        description: "Sản phẩm MacBook Air M2 hết hàng",
        status: "warning",
        created_at: new Date(Date.now() - 1000 * 60 * 60 * 3),
    },
]);

const fetchData = async () => {
    isLoading.value = true;
    try {
        // Simulate API call
        await new Promise((resolve) => setTimeout(resolve, 1000));
        // Update metrics based on selected period
        updateMetricsForPeriod();
    } catch (error) {
        console.error("Error fetching analytics data:", error);
    } finally {
        isLoading.value = false;
    }
};

const refreshData = async () => {
    await fetchData();
};

const updateMetricsForPeriod = () => {
    // Mock data update based on period
    const periodMultiplier = {
        7: 0.3,
        30: 1,
        90: 2.5,
        365: 8,
    };

    const multiplier = periodMultiplier[selectedPeriod.value] || 1;
    metrics.value.totalRevenue = Math.round(125000000 * multiplier);
    metrics.value.totalOrders = Math.round(1247 * multiplier);
    metrics.value.newCustomers = Math.round(89 * multiplier);
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
    }).format(new Date(date));
};

const getActivityIcon = (type) => {
    const icons = {
        order: ShoppingBagIcon,
        product: DevicePhoneMobileIcon,
        customer: UsersIcon,
        category: TagIcon,
    };
    return icons[type] || PlusIcon;
};

const getActivityStatusClass = (status) => {
    const classes = {
        success: "bg-green-100 text-green-800",
        warning: "bg-yellow-100 text-yellow-800",
        info: "bg-blue-100 text-blue-800",
        error: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getActivityStatusText = (status) => {
    const statusMap = {
        success: "Thành công",
        warning: "Cảnh báo",
        info: "Thông tin",
        error: "Lỗi",
    };
    return statusMap[status] || status;
};

watch(selectedPeriod, () => {
    updateMetricsForPeriod();
});

onMounted(() => {
    fetchData();
});
</script>
