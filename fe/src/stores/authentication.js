import { defineStore } from "pinia";
import api from "../boot/axios";
import { reactive } from "vue";

export const useAuthStore = defineStore("userStore", () => {
  let user = reactive({ id: null, first_name: null, last_name: null, user_type: '', value: { user_type: '' } });

  const userData = JSON.parse(localStorage.getItem('user_data'))

  user = userData || user

  function login(data) {
    return api.post("login", data);
  }

  function update(data) {
    const formData = new FormData();

    for(const [key, value] of Object.entries(data)) {
      formData.append(key, value)
    }

    return api.post(`update`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
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

  function finishDrill(data) {
    return api.post("finish-drill", data)
  }

  return { user, login, register, logout, authUser, update, finishDrill };
});
