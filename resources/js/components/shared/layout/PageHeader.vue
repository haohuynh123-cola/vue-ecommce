<template>
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="mt-2 text-gray-600">
                    {{ subtitle }}
                </p>
            </div>

            <div v-if="$slots.actions" class="flex items-center space-x-3">
                <slot name="actions" />
            </div>
        </div>

        <div v-if="$slots.breadcrumb" class="mt-4">
            <slot name="breadcrumb" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: null,
    },
});
</script>

<style scoped>
.page-header {
    @apply mb-8;
}
</style>
