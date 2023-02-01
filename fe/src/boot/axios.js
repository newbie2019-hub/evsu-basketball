import axios from "axios";
import Cookies from "js-cookie";
import { useToast } from "vue-toastification";
import router from "../router";

const toast = useToast();

const api = axios.create({
  // baseURL: "https://api.evsu-basketball.online/api",
  baseURL: "http://127.0.0.1:8000/api",
});

api.interceptors.request.use(
  (config) => {
    config.headers["Authorization"] = "Bearer " + Cookies.get("access_token");
    return config;
  },
  (error) => {
    return error.response;
  }
);

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response.status === 401) {
      Cookies.remove("access_token");
      localStorage.removeItem("user_data");
      await router().go("/");
    }
    showToastMessage(error.response.data);
    return error.response;
  }
);

const showToastMessage = (data) => {
  if (data.msg) return toast.error(data.msg);

  if (data.message) {
    return toast.error(data.message);
  }

  if (data?.errors && Array.isArray(data?.errors)) {
    return toast.error("Something went wrong! Unprocessable request");
  }
};

export default api;
