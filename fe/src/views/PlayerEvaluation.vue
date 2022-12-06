<template>
  <div class="q-my-md">
    <q-table
      flat
      class="my-sticky-column-table"
      :rows="perfEvalStore.performances.data"
      :columns="columns"
      row-key="id"
      :filter="pagination.search"
      v-model:pagination="pagination"
      :loading="loading"
      @request="getData"
    >
      <template #body-cell-description="props">
        <q-td :props="props" class="ellipsis" style="max-width: 240px">
          {{ props.row.description }}
        </q-td>
      </template>
      <template #top-left>
        <div>
          <q-btn
            :to="{ name: 'New Performance' }"
            flat
            no-caps
            icon="mdi-table-large-plus"
            color="primary"
            unelevated
            label="New Evaluation"
            style="font-size: 0.85rem"
          />
        </div>
      </template>
      <template #top-right>
        <div class="flex items-center q-gutter-sm">
          <q-input
            dense
            debounce="300"
            v-model="pagination.search"
            placeholder="Search"
          >
            <template #append>
              <q-icon name="search" />
            </template>
          </q-input>
          <div>
            <q-btn
              v-if="!pagination.filter"
              icon="mdi-filter-menu-outline"
              round
              flat
              size="10px"
              @click="pagination.filter = true"
            />
            <q-btn
              v-else
              icon="mdi-filter-minus-outline"
              round
              flat
              size="10px"
              @click="pagination.filter = false"
            />
          </div>
        </div>
      </template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <div
            class="flex no-wrap q-gutter-sm items-center"
            style="margin-top: -5px; padding-left: 15px; padding-right: 15px"
          >
            <q-btn
              @click.prevent="
                confirmDelete = true;
                selectedPerformance = props.row;
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-trash-can-outline"
            />
          </div>
        </q-td>
      </template>
    </q-table>
  </div>

  <q-dialog v-model="confirmDelete" persistent>
    <q-card style="max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Confirm Delete
        </p>
        <p class="q-mb-none q-mt-sm">
          Are you sure you want to delete this evaluation? All of its related
          data will be deleted as well.
        </p>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          flat
          label="Cancel"
          color="primary"
          v-close-popup
          style="font-size: 0.8rem"
          :disable="isBtnLoading"
        />
        <q-btn
          @click.prevent="deletePerformance"
          flat
          label="Delete"
          color="red-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script setup>
// import { useEvaluationStore } from "../stores/evaluation.js";
import { usePerfEvalStore } from "../stores/performance-evaluation.js";
import { onBeforeMount, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";

const columns = [
  {
    name: "athlete",
    label: "Athlete",
    align: "left",
    field: (row) => row.user.first_name + " " + row.user.last_name,
    sortable: false,
  },
  {
    name: "email",
    label: "Email",
    align: "left",
    field: (row) => row.user.email,
    sortable: false,
  },
  {
    name: "height",
    label: "Height",
    align: "left",
    field: (row) => row.user.height + " cm",
    sortable: false,
  },
  {
    name: "weight",
    label: "Weight",
    align: "left",
    field: (row) => row.user.weight + " kg",
    sortable: false,
  },
  {
    name: "comment",
    label: "Comment",
    align: "left",
    field: (row) => row.comment,
    sortable: false,
  },
  {
    name: "evaluated_on",
    label: "Evaluated On",
    align: "left",
    field: (row) => row.created_at,
    sortable: false,
  },
  {
    name: "actions",
    label: "Actions",
    align: "left",
    sortable: false,
  },
];

const evaluationData = ref({ evaluation: "", categories: [{ category: "" }] });
const selectedPerformance = ref({});
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);

let { pagination } = useServerPaginate();
const { required, minLength } = useFieldRules();
const perfEvalStore = usePerfEvalStore();

const isBtnLoading = ref(false);
const toast = useToast();

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);

onBeforeMount(async () => {
  await getData();
});

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await perfEvalStore.get(initialPage);

  if (status == 200) {
    perfEvalStore.performances = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const deletePerformance = async () => {
  isBtnLoading.value = true;
  const { data, status } = await perfEvalStore.deletePerformance(
    selectedPerformance.value.id
  );

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};
</script>

<style>
.q-table__top.relative-position.row.items-center {
  padding: 0 !important;
}

.my-sticky-column-table thead tr:last-child th:last-child {
  background-color: #fff;
  display: none;
}

.my-sticky-column-table td:last-child {
  background-color: #ffffff;
}

.my-sticky-column-table td:last-child {
  position: absolute;
  right: 0;
  z-index: 1;
  display: none;
}

.my-sticky-column-table tbody tr:hover td:last-child {
  display: block;
}

.my-sticky-column-table tbody tr:hover {
  cursor: pointer;
}
</style>
