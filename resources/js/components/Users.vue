<template>
    <div>
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Users Management
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Manage user accounts and permissions
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-medium text-gray-900">All Users</h3>
            </div>
            <div class="card-body">
                <div v-if="isLoading" class="flex justify-center py-8">
                    <div class="loading-spinner-lg"></div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    Users management coming soon...
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const isLoading = ref(false);

onMounted(() => {
    // Load users
});
</script>
