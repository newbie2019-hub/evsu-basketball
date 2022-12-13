import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useDrillsStore = defineStore("drillStore", () => {
  let drills = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`drills?page=${p}&search=${search}&per_page=${perPage}`);
  }

  function drillsOptions(search){
    return api.get(`drills/getDrills?search=${search}`)
  }

  function deleteDrill(data) {
    return api.delete(`drills/${data}`);
  }

  function update(data) {
    return api.put(`drills/${data.id}`, data);
  }

  function create(data) {
    return api.post("drills", data);
  }

  return { drills, get, deleteDrill, update, create, drillsOptions };
});
