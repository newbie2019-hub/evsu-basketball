import { defineStore } from "pinia";
import api from "../api";
import { ref, computed } from "vue";

export const useAthleteStore = defineStore("athleteStore", () => {
  let athletes = ref([]);

  function get() {
    return api.get("athletes");
  }

  function deleteAthlete(data) {
    return api.delete("athletes", data);
  }

  function update(data) {
    return api.put(`athletes/${data.id}`, data);
  }

  function create(data) {
    return api.post("athletes", data);
  }
  
  return { athletes, get, deleteAthlete, update, create };
});
