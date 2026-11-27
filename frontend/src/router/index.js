import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        name: "home",
        // Lazy loading
        component: () => import("../views/HomeView.vue"),
    },
    {
        path: "/login",
        name: "login",
        component: () => import("../views/LoginView.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
