import { defineStore } from "pinia";
import { ref, computed } from "vue";
import http, { fetchCsrfCookie } from "../lib/http";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const hasCheckedSession = ref(false);

    // Tem usuário?
    const isLoggedIn = computed(() => user.value !== null);

    async function fetchUser() {
        try {
            const response = await http.get("/user");

            user.value = response.data.data;
        } catch (error) {
            if (error.status === 401) {
                user.value = null;
            } else {
                throw error;
            }
        } finally {
            hasCheckedSession.value = true;
        }
    }

    // Sem try/catch: 422 sobe para a view, que é quem mostra o erro
    async function login(credentials) {
        // Grava o cookie XSRF-TOKEN; sem ele o POST volta 419
        await fetchCsrfCookie();

        const response = await http.post("/login", credentials);

        // UserResource envelopa o .data
        user.value = response.data.data;
    }

    // Sem try/catch: 422 sobe para a view, que é quem mostra o erro
    async function register(data) {
        // Grava o cookie XSRF-TOKEN; sem ele o POST volta 419
        await fetchCsrfCookie();

        const response = await http.post("/register", data);

        // UserResource envelopa o .data
        user.value = response.data.data;
    }

    async function logout() {
        try {
            await http.post("/logout");
        } finally {
            user.value = null;
        }
    }

    return {
        user,
        hasCheckedSession,
        isLoggedIn,
        fetchUser,
        login,
        register,
        logout,
    };
});
