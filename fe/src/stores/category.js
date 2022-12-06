import { defineStore } from "pinia";
import api from "../boot/axios";
import { ref, computed } from "vue";
import { useDrillsStore } from "./drills";

export const useCategoryStore = defineStore("categoryStore", () => {
  const drillStore = useDrillsStore();

  let categories = ref([]);

  function getAll() {
    return api.get(`categories/all`);
  }

  function get(page) {
    const prop = typeof page === "object";
    const p = prop ? page.pagination.page : page;
    const search = prop ? page.pagination.search : "";
    const perPage = prop ? page.pagination.rowsPerPage : 10;

    return api.get(`categories?page=${p}&search=${search}&per_page=${perPage}`);
  }

  function deleteCategory(data) {
    return api.delete(`categories/${data}`);
  }

  function update(data) {
    console.log("Passed Data: ", data);
    return api.put(`categories/${data.id}`, data);
  }

  function create(data) {
    return api.post("categories", data);
  }

  async function updateDillStore() {
    const { status, data } = await drillStore.get();
    drillStore.drills = data;
  }

  return { categories, get, getAll, deleteCategory, update, create, updateDillStore };
});
