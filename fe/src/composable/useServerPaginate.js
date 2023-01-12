import { ref } from "vue";

export function useServerPaginate() {
  const pagination = ref({
    page: 1,
    last_page: 1,
    rowsPerPage: 10,
    rowsNumber: 0,
    from: 0,
    to: 0,
    total: 0,
    descending: false,
    sortBy: "asc",
    search: "",
    filter: false,
    filter_by_team: "",
    filter_date: "",
    school_year: "S.Y 2022-2023",
    assignedPlayers: "",
    assignedDrills: "",
  });

  const schoolYear = ref([
    "S.Y 2018-2019",
    "S.Y 2019-2020",
    "S.Y 2020-2021",
    "S.Y 2021-2022",
    "S.Y 2022-2023",
    "S.Y 2023-2024",
    "S.Y 2024-2025",
    "S.Y 2025-2026",
    "S.Y 2026-2027",
    "S.Y 2027-2028",
    "S.Y 2028-2020",
    "S.Y 2029-2030",
    "S.Y 2030-2031",
    "S.Y 2031-2032",
    "S.Y 2032-2033",
    "S.Y 2033-2034",
    "S.Y 2034-2035",
    "S.Y 2035-2036",
    "S.Y 2036-2037",
    "S.Y 2037-2038",
    "S.Y 2038-2039",
    "S.Y 2039-2040",
  ]);

  return { pagination, schoolYear };
}
