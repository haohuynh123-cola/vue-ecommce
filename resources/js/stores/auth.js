import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user") || "null"),
        token: localStorage.getItem("auth_token"),
        isLoading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
    },

    actions: {
        async login(credentials) {
            this.isLoading = true;
            try {
                const response = await axios.post("/auth/login", credentials);
                const { user, token } = response.data.data;

                this.user = user;
                this.token = token;
                localStorage.setItem("auth_token", token);
                localStorage.setItem("user", JSON.stringify(user));

                return response.data;
            } catch (error) {
                throw error.response?.data || error;
            } finally {
                this.isLoading = false;
            }
        },

        async register(userData) {
            this.isLoading = true;
            try {
                const response = await axios.post("/auth/register", userData);
                return response.data;
            } catch (error) {
                throw error.response?.data || error;
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            this.isLoading = true;
            try {
                await axios.post("/auth/logout");
            } catch (error) {
                console.error("Logout error:", error);
            } finally {
                this.user = null;
                this.token = null;
                localStorage.removeItem("auth_token");
                localStorage.removeItem("user");
                this.isLoading = false;
            }
        },

        async refreshToken() {
            try {
                const response = await axios.post("/auth/refresh");
                const { user, token } = response.data.data;

                this.user = user;
                this.token = token;
                localStorage.setItem("auth_token", token);
                localStorage.setItem("user", JSON.stringify(user));

                return response.data;
            } catch (error) {
                this.logout();
                throw error;
            }
        },

        async getProfile() {
            try {
                const response = await axios.get("/auth/profile");
                this.user = response.data.data.user;
                localStorage.setItem("user", JSON.stringify(this.user));
                return response.data;
            } catch (error) {
                throw error.response?.data || error;
            }
        },

        async updateProfile(userData) {
            try {
                const response = await axios.put("/auth/profile", userData);
                this.user = response.data.data.user;
                localStorage.setItem("user", JSON.stringify(this.user));
                return response.data;
            } catch (error) {
                throw error.response?.data || error;
            }
        },

        checkAuth() {
            const token = localStorage.getItem("auth_token");
            const user = localStorage.getItem("user");

            if (token && user) {
                this.token = token;
                this.user = JSON.parse(user);
            }
        },
    },
});
