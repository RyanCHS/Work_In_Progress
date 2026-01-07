import axios from "axios";
import { useAuth } from "@composables/useAuth";

// Buat instance axios
const apiClient = axios.create({
  baseURL: "/api", // Sesuaikan jika endpoint kamu beda
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Interceptor untuk menyisipkan token jika ada
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const { logout } = useAuth();

    if (error.status === 401) {
      await logout();
    }

    return Promise.reject(error);
  }
);

export default apiClient;
