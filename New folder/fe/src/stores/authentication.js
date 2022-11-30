import { defineStore } from "pinia";
import api from "../boot/axios";
import { reactive, computed } from "vue";

export const useAuthStore = defineStore("userStore", () => {
  let user = reactive({ id: null, first_name: null, last_name: null });

  function login(data) {
    return api.post("login", data);
  }

  function register(data) {
    return api.post("register", data);
  }

  function logout() {
    return api.post("logout");
  }

  function authUser() {
    return api.get("authUser");
  }

  return { user, login, register, logout, authUser };
});