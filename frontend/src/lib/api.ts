import axios from "axios";

const api = axios.create({
    baseURL: import.meta.env.VITE_BASE_API_URL, // Laravel URL
    withCredentials: true,
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
});

// --- INTERCEPTORS ---

/*
    Intercept outgoing request and check if the token exists,

    If token exists:

    Add Authorization header with the bearer token,

    Otherwise:

    Just pass the request to the server.
*/

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = 'Bearer ' + token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
})

/*
    Intercept incoming response from the server and depending on the returned HTTP status:

    401:
    If token existed, delete it and throw expired error, otherwise throw unauthenticated error,

    403:
    Return that the user is forbidden (unauthorized) from doing that action.
*/

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