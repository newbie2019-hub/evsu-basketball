import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useGameScheduleStore = defineStore("gameScheduleStore", () => {
  let gameschedule = ref([]);
  let schedules = ref([]);

  function get(page) {
    let to = "",
      from = "";

    const prop = typeof page === "object";
    const p = prop ? page.pagination.page : page;
    const search = prop ? page.pagination.search : "";
    const perPage = prop ? page.pagination.rowsPerPage : 10;

    if (prop) {
      if (typeof page.pagination.filter_date == "object") {
        to = page.pagination.filter_date.to;
        from = page.pagination.filter_date.from;
      } else {
        from = page.pagination.filter_date;
      }
    }

    return api.get(
      `gameschedule?page=${p}&search=${search}&per_page=${perPage}&filter_from=${from}&filter_to=${to}`
    );
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

  function getSchedules() {
    return api.get("schedules");
  }

  return {
    gameschedule,
    get,
    deleteSchedule,
    update,
    create,
    getSchedules,
    schedules,
  };
});
