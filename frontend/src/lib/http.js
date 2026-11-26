import axios from "axios";

const http = axios.create({
    baseURL: "/api",
    // Na mesma origem os dois já são o padrão
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        // Faz o Laravel responder erros (422, 401) em JSON, não com redirect/HTML.
        Accept: "application/json",
    },
});

// Grava o cookie XSRF-TOKEN
// baseURL "/" porque essa rota do Sanctum fica fora do prefixo /api.
export function fetchCsrfCookie() {
    return http.get("/sanctum/csrf-cookie", { baseURL: "/" });
}

export default http;
