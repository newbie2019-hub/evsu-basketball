import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useStatisticsStore = defineStore("statisticsStore", () => {
  let statistics = ref([]);

  function get(page) {
    const prop = typeof page === "object";
    const p = prop ? page.pagination.page : page;
    const search = prop ? page.pagination.search : "";
    const sortBy = prop ? page.pagination.sortBy : "";
    const descending = prop ? page.pagination.descending : "";
    const perPage = prop ? page.pagination.rowsPerPage : 10;
    const schoolYear = prop ? page.pagination.school_year ? page.pagination.school_year : '' : '';

    return api.get(
      `statistics?page=${p}&search=${search}&per_page=${perPage}&sortBy=${sortBy}&descending=${descending}&schoolYear=${schoolYear}`
    );
  }

  function deleteStatistics(data) {
    return api.delete(`statistics/${data}`);
  }

  function update(data) {
    return api.put(`statistics/${data.id}`, data);
  }

  function create(data) {
    return api.post("statistics", data);
  }

  return {
    statistics,
    get,
    deleteStatistics,
    update,
    create,
  };
});
