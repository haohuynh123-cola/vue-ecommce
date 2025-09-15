<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Quản lý sản phẩm
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Quản lý danh mục sản phẩm công nghệ của cửa hàng
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button
                        @click="refreshData"
                        :disabled="isLoading"
                        class="p-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 disabled:opacity-50 transition-colors"
                    >
                        <ArrowPathIcon
                            class="h-5 w-5"
                            :class="{ 'animate-spin': isLoading }"
                        />
                    </button>
                    <router-link
                        to="/products/create"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        Thêm sản phẩm
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                    >
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>

                <!-- Category Filter -->
                <select
                    v-model="selectedCategory"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Tất cả danh mục</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>

                <!-- Status Filter -->
                <select
                    v-model="selectedStatus"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang bán</option>
                    <option value="inactive">Ngừng bán</option>
                    <option value="out_of_stock">Hết hàng</option>
                </select>

                <!-- Sort -->
                <select
                    v-model="sortBy"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="created_at">Mới nhất</option>
                    <option value="name">Tên A-Z</option>
                    <option value="price_asc">Giá thấp đến cao</option>
                    <option value="price_desc">Giá cao đến thấp</option>
                    <option value="stock">Tồn kho</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Danh sách sản phẩm ({{ filteredProducts.length }} sản
                        phẩm)
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'p-2 rounded-lg transition-colors',
                                viewMode === 'grid'
                                    ? 'bg-blue-100 text-blue-600'
                                    : 'text-gray-400 hover:text-gray-600',
                            ]"
                        >
                            <Squares2X2Icon class="h-5 w-5" />
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'p-2 rounded-lg transition-colors',
                                viewMode === 'list'
                                    ? 'bg-blue-100 text-blue-600'
                                    : 'text-gray-400 hover:text-gray-600',
                            ]"
                        >
                            <ListBulletIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-12">
                <div
                    class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"
                ></div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="filteredProducts.length === 0"
                class="text-center py-12"
            >
                <DevicePhoneMobileIcon
                    class="h-12 w-12 text-gray-300 mx-auto mb-4"
                />
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Không tìm thấy sản phẩm
                </h3>
                <p class="text-gray-500 mb-4">
                    Hãy thử thay đổi bộ lọc hoặc thêm sản phẩm mới
                </p>
                <router-link
                    to="/products/create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <PlusIcon class="h-5 w-5 mr-2" />
                    Thêm sản phẩm đầu tiên
                </router-link>
            </div>

            <!-- Products List -->
            <div v-else class="p-6">
                <!-- Grid View -->
                <div
                    v-if="viewMode === 'grid'"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                >
                    <div
                        v-for="product in paginatedProducts"
                        :key="product.id"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow group"
                    >
                        <!-- Product Image -->
                        <div
                            class="aspect-square bg-gray-100 relative overflow-hidden"
                        >
                            <img
                                :src="
                                    product.image ||
                                    '/images/placeholder-product.jpg'
                                "
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div class="absolute top-2 right-2">
                                <span
                                    :class="getStatusClass(product.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{ getStatusText(product.status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3
                                class="font-semibold text-gray-900 mb-2 line-clamp-2"
                            >
                                {{ product.name }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                                {{ product.description }}
                            </p>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-lg font-bold text-blue-600">{{
                                    formatCurrency(product.price)
                                }}</span>
                                <span class="text-sm text-gray-500"
                                    >Tồn: {{ product.stock || 0 }}</span
                                >
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="editProduct(product)"
                                    class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                >
                                    Chỉnh sửa
                                </button>
                                <button
                                    @click="deleteProduct(product)"
                                    class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div v-else class="space-y-4">
                    <div
                        v-for="product in paginatedProducts"
                        :key="product.id"
                        class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <!-- Product Image -->
                        <div
                            class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0"
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

                        <!-- Product Info -->
                        <div class="flex-1 ml-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        {{ product.name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ product.description }}
                                    </p>
                                    <div
                                        class="flex items-center space-x-4 mt-2"
                                    >
                                        <span class="text-sm text-gray-600"
                                            >Danh mục:
                                            {{
                                                product.category?.name ||
                                                "Chưa phân loại"
                                            }}</span
                                        >
                                        <span class="text-sm text-gray-600"
                                            >Tồn kho:
                                            {{ product.stock || 0 }}</span
                                        >
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600">
                                        {{ formatCurrency(product.price) }}
                                    </p>
                                    <span
                                        :class="getStatusClass(product.status)"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                    >
                                        {{ getStatusText(product.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2 ml-4">
                            <button
                                @click="editProduct(product)"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                            >
                                <PencilIcon class="h-4 w-4" />
                            </button>
                            <button
                                @click="deleteProduct(product)"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div
                    v-if="totalPages > 1"
                    class="mt-6 flex items-center justify-between"
                >
                    <div class="text-sm text-gray-500">
                        Hiển thị {{ (currentPage - 1) * itemsPerPage + 1 }} đến
                        {{
                            Math.min(
                                currentPage * itemsPerPage,
                                filteredProducts.length
                            )
                        }}
                        trong tổng số {{ filteredProducts.length }} sản phẩm
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="currentPage = Math.max(1, currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Trước
                        </button>
                        <span class="px-3 py-2 text-sm text-gray-700">
                            Trang {{ currentPage }} / {{ totalPages }}
                        </span>
                        <button
                            @click="
                                currentPage = Math.min(
                                    totalPages,
                                    currentPage + 1
                                )
                            "
                            :disabled="currentPage === totalPages"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Sau
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";
import {
    ArrowPathIcon,
    PlusIcon,
    MagnifyingGlassIcon,
    Squares2X2Icon,
    ListBulletIcon,
    DevicePhoneMobileIcon,
    TrashIcon,
    PencilIcon,
} from "@heroicons/vue/24/outline";

const products = ref([]);
const categories = ref([]);
const isLoading = ref(false);
const viewMode = ref("grid");
const searchQuery = ref("");
const selectedCategory = ref("");
const selectedStatus = ref("");
const sortBy = ref("created_at");
const currentPage = ref(1);
const itemsPerPage = 12;

const filteredProducts = computed(() => {
    let filtered = [...products.value];

    // Search filter
    if (searchQuery.value) {
        filtered = filtered.filter(
            (product) =>
                product.name
                    .toLowerCase()
                    .includes(searchQuery.value.toLowerCase()) ||
                product.description
                    ?.toLowerCase()
                    .includes(searchQuery.value.toLowerCase())
        );
    }

    // Category filter
    if (selectedCategory.value) {
        filtered = filtered.filter(
            (product) =>
                product.category_id === parseInt(selectedCategory.value)
        );
    }

    // Status filter
    if (selectedStatus.value) {
        filtered = filtered.filter(
            (product) => product.status === selectedStatus.value
        );
    }

    // Sort
    filtered.sort((a, b) => {
        switch (sortBy.value) {
            case "name":
                return a.name.localeCompare(b.name);
            case "price_asc":
                return a.price - b.price;
            case "price_desc":
                return b.price - a.price;
            case "stock":
                return (b.stock || 0) - (a.stock || 0);
            default:
                return new Date(b.created_at) - new Date(a.created_at);
        }
    });

    return filtered;
});

const totalPages = computed(() =>
    Math.ceil(filteredProducts.value.length / itemsPerPage)
);

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredProducts.value.slice(start, end);
});

const fetchProducts = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get("/v1/admin/products");
        if (response.data.success) {
            products.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching products:", error);
        products.value = [];
    } finally {
        isLoading.value = false;
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get("/v1/admin/categories");
        if (response.data.success) {
            categories.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching categories:", error);
        categories.value = [];
    }
};

const refreshData = async () => {
    await Promise.all([fetchProducts(), fetchCategories()]);
};

const editProduct = (product) => {
    // Navigate to edit page
    console.log("Edit product:", product);
};

const deleteProduct = (product) => {
    if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${product.name}"?`)) {
        console.log("Delete product:", product);
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(amount);
};

const getStatusClass = (status) => {
    const classes = {
        active: "bg-green-100 text-green-800",
        inactive: "bg-gray-100 text-gray-800",
        out_of_stock: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
};

const getStatusText = (status) => {
    const statusMap = {
        active: "Đang bán",
        inactive: "Ngừng bán",
        out_of_stock: "Hết hàng",
    };
    return statusMap[status] || status;
};

// Watch for filter changes
watch([searchQuery, selectedCategory, selectedStatus, sortBy], () => {
    currentPage.value = 1;
});

onMounted(() => {
    fetchProducts();
    fetchCategories();
});
</script>
