import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";

export const useAthleteStore = defineStore("athleteStore", () => {
  let athletes = ref([]);

  function getAthlete(id){
    return api.get(`athletes/${id}`)
  }

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const sortBy = prop ? page.pagination.sortBy : ''
    const descending = prop ? page.pagination.descending : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10
    const filterByTeam = prop ? page.pagination.filter_by_team : '';

    return api.get(`athletes?page=${p}&search=${search}&per_page=${perPage}&sortBy=${sortBy}&descending=${descending}&filter_by_team=${filterByTeam}`);
  }

  function athleteOptions(search){
    return api.get(`getAthletes?search=${search}`)
  }

  function deleteAthlete(data) {
    return api.delete(`athletes/${data}`);
  }

  function update(data) {
    console.log('Passed Data: ', data)
    return api.put(`athletes/${data.id}`, data);
  }

  function create(data) {
    return api.post("athletes", data);
  }

  return { athletes, get, getAthlete, deleteAthlete, update, create, athleteOptions };
});
