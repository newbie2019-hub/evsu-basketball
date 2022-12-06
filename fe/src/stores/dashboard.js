import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useDashboardStore = defineStore("dashboardStore", () => {
  let dashboard = ref([]);

  function get(page) {
    return api.get(`dashboard`);
  }

  return { dashboard, get };
});
