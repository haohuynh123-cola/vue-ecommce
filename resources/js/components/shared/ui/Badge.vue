<template>
    <span :class="badgeClasses">
        <slot />
    </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: "default",
        validator: (value) =>
            [
                "default",
                "primary",
                "secondary",
                "success",
                "warning",
                "danger",
                "info",
                "gray",
            ].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
    rounded: {
        type: Boolean,
        default: true,
    },
    dot: {
        type: Boolean,
        default: false,
    },
});

const badgeClasses = computed(() => {
    const baseClasses = [
        "inline-flex",
        "items-center",
        "font-medium",
        "transition-colors",
        "duration-200",
    ];

    // Size classes
    const sizeClasses = {
        sm: ["px-2", "py-0.5", "text-xs"],
        md: ["px-2.5", "py-0.5", "text-sm"],
        lg: ["px-3", "py-1", "text-sm"],
    };

    // Rounded classes
    const roundedClasses = props.rounded ? ["rounded-full"] : ["rounded"];

    // Variant classes
    const variantClasses = {
        default: ["bg-gray-100", "text-gray-800"],
        primary: ["bg-blue-100", "text-blue-800"],
        secondary: ["bg-gray-100", "text-gray-800"],
        success: ["bg-green-100", "text-green-800"],
        warning: ["bg-yellow-100", "text-yellow-800"],
        danger: ["bg-red-100", "text-red-800"],
        info: ["bg-blue-100", "text-blue-800"],
        gray: ["bg-gray-100", "text-gray-800"],
    };

    // Dot classes
    const dotClasses = props.dot ? ["w-2", "h-2", "rounded-full", "p-0"] : [];

    return [
        ...baseClasses,
        ...sizeClasses[props.size],
        ...roundedClasses,
        ...variantClasses[props.variant],
        ...dotClasses,
    ];
});
</script>
