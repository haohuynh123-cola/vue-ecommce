<template>
    <div class="activity-logs-container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Activity Logs</h2>
            <div class="flex space-x-2">
                <button
                    @click="refreshData"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                <button
                    @click="showCleanModal = true"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                >
                    <i class="fas fa-trash mr-2"></i>
                    Clean Old
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Log Name</label
                    >
                    <select
                        v-model="filters.log_name"
                        @change="applyFilters"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    >
                        <option value="">All</option>
                        <option value="web">Web</option>
                        <option value="api">API</option>
                        <option value="admin">Admin</option>
                        <option value="auth">Auth</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Event</label
                    >
                    <select
                        v-model="filters.event"
                        @change="applyFilters"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    >
                        <option value="">All</option>
                        <option value="viewed">Viewed</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >User</label
                    >
                    <input
                        v-model="filters.user_id"
                        type="number"
                        placeholder="User ID"
                        @change="applyFilters"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Search</label
                    >
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search description..."
                        @input="debounceSearch"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                    />
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div
            class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6"
            v-if="statistics"
        >
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">
                            Total Activities
                        </p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ statistics.total_activities }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">
                            Unique Users
                        </p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ statistics.unique_users }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-tag text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">
                            Most Active Log
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ getMostActiveLog() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">
                            Most Common Event
                        </p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ getMostCommonEvent() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                User
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Description
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Event
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                IP Address
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Time
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="loading" class="animate-pulse">
                            <td colspan="6" class="px-6 py-4 text-center">
                                <div class="flex justify-center">
                                    <i
                                        class="fas fa-spinner fa-spin text-blue-600"
                                    ></i>
                                    <span class="ml-2">Loading...</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="activities.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                No activities found
                            </td>
                        </tr>
                        <tr
                            v-else
                            v-for="activity in activities"
                            :key="activity.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center"
                                        >
                                            <i
                                                class="fas fa-user text-gray-600"
                                            ></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                activity.causer?.name || "Guest"
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{
                                                activity.causer?.email || "N/A"
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ activity.formatted_description }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ activity.url }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getEventBadgeClass(activity.event)"
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{ activity.event || "N/A" }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ activity.ip_address || "N/A" }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ activity.time_ago }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                            >
                                <button
                                    @click="viewActivity(activity)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="pagination"
                class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            @click="goToPage(pagination.current_page - 1)"
                            :disabled="pagination.current_page <= 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button
                            @click="goToPage(pagination.current_page + 1)"
                            :disabled="
                                pagination.current_page >= pagination.last_page
                            "
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{
                                    (pagination.current_page - 1) *
                                        pagination.per_page +
                                    1
                                }}</span>
                                to
                                <span class="font-medium">{{
                                    Math.min(
                                        pagination.current_page *
                                            pagination.per_page,
                                        pagination.total
                                    )
                                }}</span>
                                of
                                <span class="font-medium">{{
                                    pagination.total
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                            >
                                <button
                                    v-for="page in getPageNumbers()"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        page === pagination.current_page
                                            ? 'bg-blue-50 border-blue-500 text-blue-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Detail Modal -->
        <div
            v-if="selectedActivity"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Activity Details
                        </h3>
                        <button
                            @click="selectedActivity = null"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Description</label
                            >
                            <p class="mt-1 text-sm text-gray-900">
                                {{ selectedActivity.formatted_description }}
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Event</label
                                >
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ selectedActivity.event || "N/A" }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Log Name</label
                                >
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ selectedActivity.log_name || "N/A" }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >IP Address</label
                                >
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ selectedActivity.ip_address || "N/A" }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Method</label
                                >
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ selectedActivity.method || "N/A" }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >URL</label
                            >
                            <p class="mt-1 text-sm text-gray-900 break-all">
                                {{ selectedActivity.url || "N/A" }}
                            </p>
                        </div>
                        <div v-if="selectedActivity.properties">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Properties</label
                            >
                            <pre
                                class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded overflow-auto"
                                >{{
                                    JSON.stringify(
                                        selectedActivity.properties,
                                        null,
                                        2
                                    )
                                }}</pre
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clean Modal -->
        <div
            v-if="showCleanModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        >
            <div
                class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
            >
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Clean Old Activities
                        </h3>
                        <button
                            @click="showCleanModal = false"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                            >Days to keep</label
                        >
                        <input
                            v-model="cleanDays"
                            type="number"
                            min="1"
                            max="365"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        />
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button
                            @click="showCleanModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                        >
                            Cancel
                        </button>
                        <button
                            @click="cleanOldActivities"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
                        >
                            Clean
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

export default {
    name: "ActivityLogs",
    setup() {
        const activities = ref([]);
        const statistics = ref(null);
        const loading = ref(false);
        const pagination = ref(null);
        const selectedActivity = ref(null);
        const showCleanModal = ref(false);
        const cleanDays = ref(90);

        const filters = ref({
            log_name: "",
            event: "",
            user_id: "",
            search: "",
        });

        const searchTimeout = ref(null);

        const debounceSearch = () => {
            clearTimeout(searchTimeout.value);
            searchTimeout.value = setTimeout(() => {
                applyFilters();
            }, 500);
        };

        const fetchActivities = async (page = 1) => {
            loading.value = true;
            try {
                const params = {
                    page,
                    per_page: 20,
                    ...filters.value,
                };

                // Remove empty filters
                Object.keys(params).forEach((key) => {
                    if (params[key] === "" || params[key] === null) {
                        delete params[key];
                    }
                });

                const response = await axios.get("/v1/admin/audits", {
                    params,
                });
                activities.value = response.data.data;
                pagination.value = response.data.meta;
            } catch (error) {
                console.error("Error fetching activities:", error);
            } finally {
                loading.value = false;
            }
        };

        const fetchStatistics = async () => {
            try {
                const response = await axios.get("/v1/admin/audits/statistics");
                statistics.value = response.data.data;
            } catch (error) {
                console.error("Error fetching statistics:", error);
            }
        };

        const applyFilters = () => {
            fetchActivities(1);
        };

        const goToPage = (page) => {
            if (page >= 1 && page <= pagination.value.last_page) {
                fetchActivities(page);
            }
        };

        const getPageNumbers = () => {
            if (!pagination.value) return [];

            const current = pagination.value.current_page;
            const last = pagination.value.last_page;
            const pages = [];

            // Show first page
            if (current > 3) {
                pages.push(1);
                if (current > 4) pages.push("...");
            }

            // Show pages around current
            for (
                let i = Math.max(1, current - 2);
                i <= Math.min(last, current + 2);
                i++
            ) {
                pages.push(i);
            }

            // Show last page
            if (current < last - 2) {
                if (current < last - 3) pages.push("...");
                pages.push(last);
            }

            return pages;
        };

        const viewActivity = (activity) => {
            selectedActivity.value = activity;
        };

        const refreshData = () => {
            fetchActivities(pagination.value?.current_page || 1);
            fetchStatistics();
        };

        const cleanOldActivities = async () => {
            try {
                await axios.delete("/admin/audits/clean", {
                    data: { days: cleanDays.value },
                });
                showCleanModal.value = false;
                refreshData();
                alert(`Cleaned activities older than ${cleanDays.value} days`);
            } catch (error) {
                console.error("Error cleaning activities:", error);
                alert("Error cleaning activities");
            }
        };

        const getMostActiveLog = () => {
            if (!statistics.value?.activities_by_log_name) return "N/A";
            const entries = Object.entries(
                statistics.value.activities_by_log_name
            );
            return entries.length > 0 ? entries[0][0] : "N/A";
        };

        const getMostCommonEvent = () => {
            if (!statistics.value?.activities_by_event) return "N/A";
            const entries = Object.entries(
                statistics.value.activities_by_event
            );
            return entries.length > 0 ? entries[0][0] : "N/A";
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
            fetchStatistics();
        });

        return {
            activities,
            statistics,
            loading,
            pagination,
            selectedActivity,
            showCleanModal,
            cleanDays,
            filters,
            debounceSearch,
            applyFilters,
            goToPage,
            getPageNumbers,
            viewActivity,
            refreshData,
            cleanOldActivities,
            getMostActiveLog,
            getMostCommonEvent,
            getEventBadgeClass,
        };
    },
};
</script>

<style scoped>
.activity-logs-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 1rem;
}
</style>
