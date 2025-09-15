<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="buttonClasses"
        @click="handleClick"
    >
        <!-- Loading spinner -->
        <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-2 h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        <!-- Icon -->
        <component v-if="icon && !loading" :is="icon" :class="iconClasses" />

        <!-- Button content -->
        <span v-if="$slots.default">
            <slot />
        </span>
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: "primary",
        validator: (value) =>
            [
                "primary",
                "secondary",
                "success",
                "warning",
                "danger",
                "outline",
                "ghost",
            ].includes(value),
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg", "xl"].includes(value),
    },
    type: {
        type: String,
        default: "button",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: [String, Object],
        default: null,
    },
    fullWidth: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["click"]);

const buttonClasses = computed(() => {
    const baseClasses = [
        "inline-flex",
        "items-center",
        "justify-center",
        "font-medium",
        "rounded-lg",
        "transition-colors",
        "duration-200",
        "focus:outline-none",
        "focus:ring-2",
        "focus:ring-offset-2",
        "disabled:opacity-50",
        "disabled:cursor-not-allowed",
    ];

    // Size classes
    const sizeClasses = {
        sm: ["px-3", "py-1.5", "text-sm"],
        md: ["px-4", "py-2", "text-sm"],
        lg: ["px-6", "py-3", "text-base"],
        xl: ["px-8", "py-4", "text-lg"],
    };

    // Variant classes
    const variantClasses = {
        primary: [
            "bg-blue-600",
            "text-white",
            "hover:bg-blue-700",
            "focus:ring-blue-500",
        ],
        secondary: [
            "bg-gray-600",
            "text-white",
            "hover:bg-gray-700",
            "focus:ring-gray-500",
        ],
        success: [
            "bg-green-600",
            "text-white",
            "hover:bg-green-700",
            "focus:ring-green-500",
        ],
        warning: [
            "bg-yellow-600",
            "text-white",
            "hover:bg-yellow-700",
            "focus:ring-yellow-500",
        ],
        danger: [
            "bg-red-600",
            "text-white",
            "hover:bg-red-700",
            "focus:ring-red-500",
        ],
        outline: [
            "border",
            "border-gray-300",
            "bg-white",
            "text-gray-700",
            "hover:bg-gray-50",
            "focus:ring-blue-500",
        ],
        ghost: [
            "bg-transparent",
            "text-gray-700",
            "hover:bg-gray-100",
            "focus:ring-gray-500",
        ],
    };

    const classes = [
        ...baseClasses,
        ...sizeClasses[props.size],
        ...variantClasses[props.variant],
    ];

    if (props.fullWidth) {
        classes.push("w-full");
    }

    return classes;
});

const iconClasses = computed(() => {
    const sizeClasses = {
        sm: "h-4 w-4",
        md: "h-4 w-4",
        lg: "h-5 w-5",
        xl: "h-6 w-6",
    };

    return [sizeClasses[props.size], "mr-2"];
});

const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit("click", event);
    }
};
</script>
