import axios from "axios";

const api = axios.create({
    baseURL: import.meta.env.VITE_BASE_API_URL, // Laravel URL
    withCredentials: true,
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = 'Bearer ' + token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
})

api.interceptors.response.use(undefined, async (error) => {
    if (error.response?.status === 401) {
        if (localStorage.getItem('token')) {
            window.dispatchEvent(new CustomEvent('session-error', { detail: 'expired' }));
            localStorage.removeItem('token');
        } else {
            window.dispatchEvent(new CustomEvent('session-error', { detail: 'unauthenticated' }));
        }
    } else if (error.response?.status === 403) {
        window.dispatchEvent(new CustomEvent('session-error', { detail: 'forbidden' }));
    }
    return Promise.reject(error);
})

export default api;