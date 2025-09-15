<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        :class="iconBgClass"
                        class="w-8 h-8 rounded-md flex items-center justify-center"
                    >
                        <UsersIcon
                            v-if="icon === 'users'"
                            class="w-5 h-5 text-white"
                        />
                        <ShoppingBagIcon
                            v-else-if="icon === 'shopping-cart'"
                            class="w-5 h-5 text-white"
                        />
                        <CurrencyDollarIcon
                            v-else-if="icon === 'currency-dollar'"
                            class="w-5 h-5 text-white"
                        />
                        <CubeIcon
                            v-else-if="icon === 'box'"
                            class="w-5 h-5 text-white"
                        />
                        <ChartBarIcon v-else class="w-5 h-5 text-white" />
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ title }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">
                                {{ value }}
                            </div>
                            <div
                                v-if="change"
                                :class="changeClass"
                                class="ml-2 flex items-baseline text-sm font-semibold"
                            >
                                <svg
                                    v-if="change > 0"
                                    class="self-center flex-shrink-0 h-4 w-4 text-green-600"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <svg
                                    v-else
                                    class="self-center flex-shrink-0 h-4 w-4 text-red-600"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                <span class="sr-only"
                                    >{{
                                        change > 0 ? "Increased" : "Decreased"
                                    }}
                                    by</span
                                >
                                {{ Math.abs(change) }}%
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div v-if="$slots.footer" class="bg-gray-50 px-5 py-3">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    UsersIcon,
    ShoppingBagIcon,
    CurrencyDollarIcon,
    CubeIcon,
    ChartBarIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    change: {
        type: Number,
        default: null,
    },
    color: {
        type: String,
        default: "blue",
        validator: (value) =>
            ["blue", "green", "yellow", "red", "purple", "indigo"].includes(
                value
            ),
    },
});

const iconBgClass = computed(() => {
    const classes = {
        blue: "bg-blue-500",
        green: "bg-green-500",
        yellow: "bg-yellow-500",
        red: "bg-red-500",
        purple: "bg-purple-500",
        indigo: "bg-indigo-500",
    };
    return classes[props.color];
});

const iconClass = computed(() => {
    return "text-white";
});

const changeClass = computed(() => {
    if (props.change === null) return "";
    return props.change >= 0 ? "text-green-600" : "text-red-600";
});

const changeIcon = computed(() => {
    if (props.change === null) return null;
    return props.change >= 0 ? "arrow-up" : "arrow-down";
});
</script>
