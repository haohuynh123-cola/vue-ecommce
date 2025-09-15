<template>
    <div :class="cardClasses">
        <!-- Card Header -->
        <div v-if="$slots.header || title" class="card-header">
            <slot name="header">
                <h3 v-if="title" class="text-lg font-medium text-gray-900">
                    {{ title }}
                </h3>
            </slot>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <slot />
        </div>

        <!-- Card Footer -->
        <div v-if="$slots.footer" class="card-footer">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    title: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: "default",
        validator: (value) =>
            ["default", "elevated", "outlined", "flat"].includes(value),
    },
    padding: {
        type: String,
        default: "default",
        validator: (value) => ["none", "sm", "default", "lg"].includes(value),
    },
    hover: {
        type: Boolean,
        default: false,
    },
});

const cardClasses = computed(() => {
    const baseClasses = ["bg-white", "rounded-lg", "border", "border-gray-200"];

    // Variant classes
    const variantClasses = {
        default: ["shadow-sm"],
        elevated: ["shadow-lg"],
        outlined: ["border-2", "border-gray-300", "shadow-none"],
        flat: ["shadow-none", "border-gray-100"],
    };

    // Padding classes
    const paddingClasses = {
        none: [],
        sm: ["p-3"],
        default: ["p-6"],
        lg: ["p-8"],
    };

    const classes = [
        ...baseClasses,
        ...variantClasses[props.variant],
        ...paddingClasses[props.padding],
    ];

    if (props.hover) {
        classes.push(
            "hover:shadow-md",
            "transition-shadow",
            "duration-200",
            "cursor-pointer"
        );
    }

    return classes;
});
</script>

<style scoped>
.card-header {
    @apply px-6 py-4 border-b border-gray-200;
}

.card-body {
    @apply px-6 py-4;
}

.card-footer {
    @apply px-6 py-4 border-t border-gray-200 bg-gray-50;
}
</style>
