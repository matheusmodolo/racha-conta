<script setup>
import { useAuthStore } from "../stores/auth";
const auth = useAuthStore();
auth.fetchUser();
</script>

<template>
    <h1>Home</h1>
    <pre>{{ auth.user }} | logado: {{ auth.isLoggedIn }} | checou: {{ auth.hasCheckedSession }}</pre>
</template>