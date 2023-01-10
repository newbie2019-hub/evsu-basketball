import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useFinishedDrillsStore = defineStore("finishedDrillStore", () => {
  let drills = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10
    const assignedDrills = prop ? page.pagination.assignedDrills : ''

    return api.get(`finished-drills?page=${p}&search=${search}&per_page=${perPage}&assigned_drills=${assignedDrills}`);
  }

  function deleteDrill(data) {
    return api.delete(`drills/${data}`);
  }

  return { get, deleteDrill, drills };
});
