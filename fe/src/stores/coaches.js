import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref } from "vue";

export const useCoachStore = defineStore("coachStore", () => {
  let coaches = ref([]);

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const sortBy = prop ? page.pagination.sortBy : ''
    const descending = prop ? page.pagination.descending : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`coaches?page=${p}&search=${search}&per_page=${perPage}&sortBy=${sortBy}&descending=${descending}`);
  }

  function coachOptions(search){
    return api.get(`getAthletes?search=${search}`)
  }

  function deleteCoach(data) {
    return api.delete(`coaches/${data}`);
  }

  function update(data) {
    console.log('Passed Data: ', data)
    return api.put(`coaches/${data.id}`, data);
  }

  function create(data) {
    return api.post("coaches", data);
  }

  function assign(data) {
    return api.post("coaches/assign", data);
  }

  return { coaches, get, deleteCoach, update, create, coachOptions, assign };
});
