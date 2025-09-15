<template>
    <div class="form-group">
        <!-- Label -->
        <label v-if="label" :for="inputId" :class="labelClasses">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <!-- Input Container -->
        <div class="relative">
            <!-- Input Icon (Left) -->
            <div
                v-if="$slots.iconLeft || iconLeft"
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
                <slot name="iconLeft">
                    <component
                        v-if="iconLeft"
                        :is="iconLeft"
                        class="h-5 w-5 text-gray-400"
                    />
                </slot>
            </div>

            <!-- Input Field -->
            <input
                :id="inputId"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :class="inputClasses"
                @input="handleInput"
                @blur="handleBlur"
                @focus="handleFocus"
            />

            <!-- Input Icon (Right) -->
            <div
                v-if="$slots.iconRight || iconRight"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <slot name="iconRight">
                    <component
                        v-if="iconRight"
                        :is="iconRight"
                        class="h-5 w-5 text-gray-400"
                    />
                </slot>
            </div>

            <!-- Clear Button -->
            <button
                v-if="clearable && modelValue && !disabled"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                @click="handleClear"
            >
                <svg
                    class="h-5 w-5 text-gray-400 hover:text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </button>
        </div>

        <!-- Help Text -->
        <p v-if="helpText && !error" class="mt-1 text-sm text-gray-500">
            {{ helpText }}
        </p>

        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    label: {
        type: String,
        default: null,
    },
    placeholder: {
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
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
    iconLeft: {
        type: [String, Object],
        default: null,
    },
    iconRight: {
        type: [String, Object],
        default: null,
    },
});

const emit = defineEmits(["update:modelValue", "blur", "focus", "clear"]);

const inputId = ref(`input-${Math.random().toString(36).substr(2, 9)}`);

const labelClasses = computed(() => {
    const baseClasses = [
        "block",
        "text-sm",
        "font-medium",
        "text-gray-700",
        "mb-1",
    ];

    if (props.error) {
        baseClasses.push("text-red-700");
    }

    return baseClasses;
});

const inputClasses = computed(() => {
    const baseClasses = [
        "block",
        "w-full",
        "border",
        "rounded-lg",
        "shadow-sm",
        "focus:ring-2",
        "focus:ring-blue-500",
        "focus:border-blue-500",
        "transition-colors",
        "duration-200",
    ];

    // Size classes
    const sizeClasses = {
        sm: ["px-3", "py-1.5", "text-sm"],
        md: ["px-4", "py-2", "text-sm"],
        lg: ["px-4", "py-3", "text-base"],
    };

    // State classes
    if (props.error) {
        baseClasses.push(
            "border-red-300",
            "focus:ring-red-500",
            "focus:border-red-500"
        );
    } else {
        baseClasses.push("border-gray-300");
    }

    if (props.disabled) {
        baseClasses.push("bg-gray-50", "text-gray-500", "cursor-not-allowed");
    }

    if (props.readonly) {
        baseClasses.push("bg-gray-50");
    }

    // Icon padding
    if (props.$slots.iconLeft || props.iconLeft) {
        baseClasses.push("pl-10");
    }

    if ((props.$slots.iconRight || props.iconRight) && !props.clearable) {
        baseClasses.push("pr-10");
    } else if (props.clearable) {
        baseClasses.push("pr-10");
    }

    return [...baseClasses, ...sizeClasses[props.size]];
});

const handleInput = (event) => {
    emit("update:modelValue", event.target.value);
};

const handleBlur = (event) => {
    emit("blur", event);
};

const handleFocus = (event) => {
    emit("focus", event);
};

const handleClear = () => {
    emit("update:modelValue", "");
    emit("clear");
};
</script>
