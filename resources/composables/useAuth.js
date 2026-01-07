import { ref } from "vue";
import { useRouter } from "vue-router";
import apiClient from "@utils/axios";

const user = ref(null);
const token = ref(localStorage.getItem("auth_token") || null);

export function useAuth() {
  const router = useRouter();

  async function login(username, password) {
    try {
      const response = await apiClient.post("/auth/login", {
        Username: username,
        Password: password,
      });
      token.value = response.data.access_token;
      localStorage.setItem("auth_token", token.value);
      await fetchUser();
      router.push("/admin/dashboard");
    } catch (error) {
      throw error; // penting agar bisa ditangkap di komponen
    }
  }

  async function fetchUser() {
    try {
      const response = await apiClient.get("/auth/me");
      user.value = response.data;
    } catch (e) {
      logout();
    }
  }

  async function logout() {
    try {
      await apiClient.post("/auth/logout");
    } catch (e) {}
    token.value = null;
    user.value = null;
    localStorage.removeItem("auth_token");
  }

  const isAuthenticated = () => !!token.value;
  const hasRole = (role) => user.value?.UserGroup === role;

  return { user, token, isAuthenticated, hasRole, login, logout, fetchUser };
}
