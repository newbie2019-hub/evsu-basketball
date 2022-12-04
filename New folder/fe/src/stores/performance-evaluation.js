import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref } from "vue";

export const usePerfEvalStore = defineStore("perfEvalStore", () => {
  let performances = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`performances?page=${p}&search=${search}&per_page=${perPage}`);
  }

  function deletePerformance(data) {
    return api.delete(`performances/${data}`);
  }

  function update(data) {
    return api.put(`performances/${data.id}`, data);
  }

  function create(data) {
    return api.post("performances", data);
  }

  return { performances, get, deletePerformance, update, create };
});
