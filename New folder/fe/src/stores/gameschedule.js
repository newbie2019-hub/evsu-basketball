import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useGameScheduleStore = defineStore("gameScheduleStore", () => {
  let gameschedule = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`gameschedule?page=${p}&search=${search}&per_page=${perPage}`);
  }

  function deleteSchedule(data) {
    return api.delete(`gameschedule/${data}`);
  }

  function update(data) {
    return api.put(`gameschedule/${data.id}`, data);
  }

  function create(data) {
    return api.post("gameschedule", data);
  }

  return { gameschedule, get, deleteSchedule, update, create };
});
