<template>
    <div class="recent-activities">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            <button
                @click="refreshActivities"
                class="text-sm text-blue-600 hover:text-blue-800"
            >
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                Refresh
            </button>
        </div>

        <div v-if="loading" class="space-y-3">
            <div v-for="n in 5" :key="n" class="animate-pulse">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-gray-300 rounded-full"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-300 rounded w-3/4 mb-1"></div>
                        <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else-if="activities.length === 0"
            class="text-center py-8 text-gray-500"
        >
            <i class="fas fa-history text-4xl mb-2"></i>
            <p>No recent activities</p>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="activity in activities"
                :key="activity.id"
                class="flex items-start space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:shadow-sm transition-shadow"
            >
                <div class="flex-shrink-0">
                    <div
                        class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center"
                    >
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900">
                            {{ activity.causer?.name || "Guest" }}
                        </p>
                        <span class="text-xs text-gray-500">{{
                            activity.time_ago
                        }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ activity.formatted_description }}
                    </p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span
                            :class="getEventBadgeClass(activity.event)"
                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        >
                            {{ activity.event || "N/A" }}
                        </span>
                        <span
                            v-if="activity.ip_address"
                            class="text-xs text-gray-500"
                        >
                            {{ activity.ip_address }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activities.length > 0" class="mt-4 text-center">
            <button
                @click="$emit('view-all')"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
                View All Activities
            </button>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";

export default {
    name: "RecentActivities",
    emits: ["view-all"],
    setup() {
        const activities = ref([]);
        const loading = ref(false);

        const fetchActivities = async () => {
            loading.value = true;
            try {
                const response = await axios.get("/admin/audits/recent", {
                    params: { limit: 10 },
                });
                activities.value = response.data.data;
            } catch (error) {
                console.error("Error fetching recent activities:", error);
            } finally {
                loading.value = false;
            }
        };

        const refreshActivities = () => {
            fetchActivities();
        };

        const getEventBadgeClass = (event) => {
            const classes = {
                viewed: "bg-blue-100 text-blue-800",
                created: "bg-green-100 text-green-800",
                updated: "bg-yellow-100 text-yellow-800",
                deleted: "bg-red-100 text-red-800",
                login: "bg-purple-100 text-purple-800",
                logout: "bg-gray-100 text-gray-800",
            };
            return classes[event] || "bg-gray-100 text-gray-800";
        };

        onMounted(() => {
            fetchActivities();
        });

        return {
            activities,
            loading,
            refreshActivities,
            getEventBadgeClass,
        };
    },
};
</script>

<style scoped>
.recent-activities {
    @apply bg-white rounded-lg shadow p-6;
}
</style>
