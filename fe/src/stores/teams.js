import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useTeamStore = defineStore("teamStore", () => {
  let teams = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const sortBy = prop ? page.pagination.sortBy : ''
    const descending = prop ? page.pagination.descending : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`teams?page=${p}&search=${search}&per_page=${perPage}&sortBy=${sortBy}&descending=${descending}`);
  }

  function athleteOptions(search){
    return api.get(`getteams?search=${search}`)
  }

  function deleteTeam(data) {
    return api.delete(`teams/${data}`);
  }

  function update(data) {
    console.log('Passed Data: ', data)
    return api.put(`teams/${data.id}`, data);
  }

  function create(data) {
    return api.post("teams", data);
  }

  return { teams, get, deleteTeam, update, create, athleteOptions };
});
