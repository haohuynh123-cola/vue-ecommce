<template>
    <div :class="groupClasses">
        <slot />
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    direction: {
        type: String,
        default: "vertical",
        validator: (value) => ["vertical", "horizontal"].includes(value),
    },
    spacing: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
});

const groupClasses = computed(() => {
    const baseClasses = [];

    // Direction classes
    if (props.direction === "horizontal") {
        baseClasses.push("flex", "items-center", "space-x-4");
    } else {
        baseClasses.push("space-y-2");
    }

    // Spacing classes
    const spacingClasses = {
        sm: props.direction === "horizontal" ? "space-x-2" : "space-y-1",
        md: props.direction === "horizontal" ? "space-x-4" : "space-y-2",
        lg: props.direction === "horizontal" ? "space-x-6" : "space-y-4",
    };

    if (props.direction === "vertical") {
        baseClasses.push(spacingClasses[props.spacing]);
    }

    return baseClasses;
});
</script>
