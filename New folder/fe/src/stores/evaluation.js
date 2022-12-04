import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref } from "vue";

export const useEvaluationStore = defineStore("evaluationStore", () => {
  let allEvaluations = ref([]);
  let evaluations = ref([]);

  function getAll() {
    return api.get(`evaluations/all`);
  }

  function get(page) {
    const prop = typeof page === 'object'
    const p =  prop ? page.pagination.page : page
    const search = prop ? page.pagination.search : ''
    const perPage = prop ? page.pagination.rowsPerPage : 10

    return api.get(`evaluations?page=${p}&search=${search}&per_page=${perPage}`);
  }

  function deleteEvaluation(data) {
    return api.delete(`evaluations/${data}`);
  }

  function update(data) {
    return api.put(`evaluations/${data.id}`, data);
  }

  function create(data) {
    return api.post("evaluations", data);
  }

  return { evaluations, get, deleteEvaluation, update, create, getAll, allEvaluations };
});
