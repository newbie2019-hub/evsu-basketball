import { ref } from 'vue'

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
    sortBy: 'asc',
    search: '',
    filter: false,
    filter_by_team: '',
    filter_date: '',
    assignedPlayers: '',
    assignedDrills: '',
  });

  return { pagination }
}
