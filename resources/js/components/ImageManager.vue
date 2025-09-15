<template>
    <div class="space-y-6">
        <!-- Upload Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Quản lý hình ảnh
            </h3>

            <!-- Upload Area -->
            <div
                @drop="handleDrop"
                @dragover.prevent
                @dragenter.prevent
                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors"
                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
            >
                <input
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/*"
                    @change="handleFileSelect"
                    class="hidden"
                />

                <div v-if="!isUploading">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        ></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900 mb-2">
                        Kéo thả hình ảnh vào đây
                    </p>
                    <p class="text-gray-500 mb-4">hoặc</p>
                    <button
                        @click="$refs.fileInput.click()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <PlusIcon class="h-5 w-5 mr-2" />
                        Chọn hình ảnh
                    </button>
                    <p class="text-sm text-gray-500 mt-2">
                        Hỗ trợ: JPEG, PNG, GIF, WebP (tối đa 10MB)
                    </p>
                </div>

                <div v-else class="flex items-center justify-center">
                    <div
                        class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"
                    ></div>
                    <span class="ml-3 text-gray-600">Đang tải lên...</span>
                </div>
            </div>
        </div>

        <!-- Image Gallery -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Thư viện hình ảnh
                </h3>
                <div class="flex items-center space-x-2">
                    <select
                        v-model="selectedType"
                        @change="fetchImages"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Tất cả loại</option>
                        <option value="thumbnail">Thumbnail</option>
                        <option value="gallery">Gallery</option>
                        <option value="banner">Banner</option>
                        <option value="avatar">Avatar</option>
                        <option value="logo">Logo</option>
                    </select>
                    <button
                        @click="fetchImages"
                        :disabled="isLoading"
                        class="p-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                    >
                        <ArrowPathIcon
                            class="h-5 w-5"
                            :class="{ 'animate-spin': isLoading }"
                        />
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center py-8">
                <div
                    class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"
                ></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="images.length === 0" class="text-center py-8">
                <PhotoIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500">Chưa có hình ảnh nào</p>
            </div>

            <!-- Image Grid -->
            <div
                v-else
                class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"
            >
                <div
                    v-for="image in images"
                    :key="image.id"
                    class="relative group cursor-pointer"
                    @click="selectImage(image)"
                >
                    <div
                        class="aspect-square bg-gray-100 rounded-lg overflow-hidden"
                    >
                        <img
                            :src="image.thumbnail_url || image.url"
                            :alt="image.alt_text"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                        />
                    </div>

                    <!-- Selection Indicator -->
                    <div
                        v-if="selectedImages.includes(image.id)"
                        class="absolute inset-0 bg-blue-500 bg-opacity-50 rounded-lg flex items-center justify-center"
                    >
                        <CheckIcon class="h-8 w-8 text-white" />
                    </div>

                    <!-- Image Info -->
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 truncate">
                            {{ image.original_name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ image.human_file_size }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div
                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <div class="flex space-x-1">
                            <button
                                @click.stop="editImage(image)"
                                class="p-1 bg-white rounded-full shadow-sm hover:bg-gray-50"
                            >
                                <PencilIcon class="h-4 w-4 text-gray-600" />
                            </button>
                            <button
                                @click.stop="deleteImage(image)"
                                class="p-1 bg-white rounded-full shadow-sm hover:bg-red-50"
                            >
                                <TrashIcon class="h-4 w-4 text-red-600" />
                            </button>
                        </div>
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
                    {{ Math.min(currentPage * itemsPerPage, totalImages) }}
                    trong tổng số {{ totalImages }} hình ảnh
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                    >
                        Trước
                    </button>
                    <span class="px-3 py-2 text-sm text-gray-700">
                        Trang {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        @click="
                            currentPage = Math.min(totalPages, currentPage + 1)
                        "
                        :disabled="currentPage === totalPages"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                    >
                        Sau
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div v-if="selectedImages.length > 0" class="bg-blue-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-blue-900">
                    {{ selectedImages.length }} hình ảnh đã chọn
                </span>
                <div class="flex items-center space-x-2">
                    <select
                        v-model="bulkAction"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Chọn hành động</option>
                        <option value="delete">Xóa</option>
                        <option value="set_thumbnail">Đặt làm thumbnail</option>
                        <option value="set_gallery">Đặt làm gallery</option>
                    </select>
                    <button
                        @click="executeBulkAction"
                        :disabled="!bulkAction"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Thực hiện
                    </button>
                    <button
                        @click="clearSelection"
                        class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                    >
                        Bỏ chọn
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import {
    PlusIcon,
    ArrowPathIcon,
    PhotoIcon,
    CheckIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/outline";

const images = ref([]);
const selectedImages = ref([]);
const selectedType = ref("");
const isLoading = ref(false);
const isUploading = ref(false);
const isDragging = ref(false);
const currentPage = ref(1);
const itemsPerPage = 12;
const totalImages = ref(0);
const bulkAction = ref("");

const totalPages = computed(() => Math.ceil(totalImages.value / itemsPerPage));

const fetchImages = async () => {
    isLoading.value = true;
    try {
        const params = new URLSearchParams({
            per_page: itemsPerPage,
            page: currentPage.value,
        });

        if (selectedType.value) {
            params.append("type", selectedType.value);
        }

        const response = await axios.get(`/api/images?${params}`);
        if (response.data.success) {
            images.value = response.data.data;
            totalImages.value = response.data.meta.total;
        }
    } catch (error) {
        console.error("Error fetching images:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    uploadImages(files);
};

const handleDrop = (event) => {
    event.preventDefault();
    isDragging.value = false;
    const files = Array.from(event.dataTransfer.files);
    uploadImages(files);
};

const uploadImages = async (files) => {
    if (files.length === 0) return;

    isUploading.value = true;
    const formData = new FormData();

    files.forEach((file, index) => {
        formData.append(`images[${index}]`, file);
    });

    formData.append("type", "gallery");

    try {
        const response = await axios.post("/api/images", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data.success) {
            await fetchImages();
            clearSelection();
        }
    } catch (error) {
        console.error("Error uploading images:", error);
    } finally {
        isUploading.value = false;
    }
};

const selectImage = (image) => {
    const index = selectedImages.value.indexOf(image.id);
    if (index > -1) {
        selectedImages.value.splice(index, 1);
    } else {
        selectedImages.value.push(image.id);
    }
};

const clearSelection = () => {
    selectedImages.value = [];
};

const editImage = (image) => {
    // TODO: Open edit modal
    console.log("Edit image:", image);
};

const deleteImage = async (image) => {
    if (
        confirm(`Bạn có chắc chắn muốn xóa hình ảnh "${image.original_name}"?`)
    ) {
        try {
            const response = await axios.delete(`/api/images/${image.id}`);
            if (response.data.success) {
                await fetchImages();
            }
        } catch (error) {
            console.error("Error deleting image:", error);
        }
    }
};

const executeBulkAction = async () => {
    if (!bulkAction.value || selectedImages.value.length === 0) return;

    try {
        switch (bulkAction.value) {
            case "delete":
                if (
                    confirm(
                        `Bạn có chắc chắn muốn xóa ${selectedImages.value.length} hình ảnh?`
                    )
                ) {
                    for (const imageId of selectedImages.value) {
                        await axios.delete(`/api/images/${imageId}`);
                    }
                    await fetchImages();
                    clearSelection();
                }
                break;
            // Add other bulk actions here
        }
    } catch (error) {
        console.error("Error executing bulk action:", error);
    } finally {
        bulkAction.value = "";
    }
};

onMounted(() => {
    fetchImages();
});
</script>
