import { ref } from 'vue'

export function useServerPaginate() {
  const pagination = ref({
    sortBy: "desc",
    descending: false,
    page: 1,
    last_page: 1,
    rowsPerPage: 10,
    rowsNumber: 0,
    from: 0,
    to: 0,
    total: 0,
    search: '',
    filter: false
  });

  return { pagination }
}
