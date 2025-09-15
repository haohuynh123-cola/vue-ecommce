<template>
    <div class="form-field">
        <label
            v-if="label"
            :for="fieldId"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <div class="relative">
            <slot :fieldId="fieldId" />
        </div>

        <p v-if="helpText && !error" class="mt-1 text-sm text-gray-500">
            {{ helpText }}
        </p>

        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    helpText: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const fieldId = ref(`field-${Math.random().toString(36).substr(2, 9)}`);
</script>
