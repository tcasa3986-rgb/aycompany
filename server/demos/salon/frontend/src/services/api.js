import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || '/api',
});

// Interceptor para inyectar el token en cada petición
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('salon_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar respuestas (ej. token expirado o inválido)
api.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && (error.response.status === 401 || error.response.status === 403)) {
            // Si el token es inválido, limpiamos localStorage y forzamos re-login
            localStorage.removeItem('salon_token');
            localStorage.removeItem('salon_user');
            // Evitar loop infinito si ya estamos en /login
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export default api;
