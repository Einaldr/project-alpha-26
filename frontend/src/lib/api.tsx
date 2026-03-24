import { useNavigate } from "react-router-dom";
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
        config.headers.Authorization = 'Bearer ${token}';
    }
    return config;
}, (error) => {
    return Promise.reject(error);
})

api.interceptors.response.use(undefined, async (error) => {
    const navigate = useNavigate();

    if (error.response?.status === 401) {
        localStorage.removeItem('token');
        navigate("/login");
    }
    return Promise.reject(error);
})

export default api;