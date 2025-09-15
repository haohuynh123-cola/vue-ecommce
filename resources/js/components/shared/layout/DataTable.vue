<template>
    <div class="data-table">
        <!-- Table Header -->
        <div v-if="$slots.header || title" class="table-header">
            <slot name="header">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ title }}
                </h3>
            </slot>
        </div>

        <!-- Table Content -->
        <div class="table-content">
            <div v-if="loading" class="flex justify-center py-8">
                <div class="loading-spinner-lg"></div>
            </div>

            <div
                v-else-if="items.length === 0"
                class="text-center py-8 text-gray-500"
            >
                {{ emptyMessage }}
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                {{ column.label }}
                            </th>
                            <th
                                v-if="$slots.actions"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="(item, index) in items"
                            :key="getItemKey(item, index)"
                            class="hover:bg-gray-50"
                        >
                            <td
                                v-for="column in columns"
                                :key="column.key"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                <slot
                                    :name="`cell-${column.key}`"
                                    :item="item"
                                    :value="getNestedValue(item, column.key)"
                                    :index="index"
                                >
                                    {{ getNestedValue(item, column.key) }}
                                </slot>
                            </td>
                            <td
                                v-if="$slots.actions"
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <slot
                                    name="actions"
                                    :item="item"
                                    :index="index"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer -->
        <div v-if="$slots.footer" class="table-footer">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    columns: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    emptyMessage: {
        type: String,
        default: "No data available",
    },
    itemKey: {
        type: [String, Function],
        default: "id",
    },
});

const getItemKey = (item, index) => {
    if (typeof props.itemKey === "function") {
        return props.itemKey(item, index);
    }
    return item[props.itemKey] || index;
};

const getNestedValue = (obj, path) => {
    return path.split(".").reduce((current, key) => {
        return current && current[key] !== undefined ? current[key] : null;
    }, obj);
};
</script>

<style scoped>
.table-header {
    @apply px-6 py-4 border-b border-gray-200;
}

.table-content {
    @apply bg-white;
}

.table-footer {
    @apply px-6 py-4 border-t border-gray-200 bg-gray-50;
}
</style>
