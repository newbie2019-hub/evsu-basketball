<template>
  <div class="q-my-md" v-if="user?.user_type !== 'admin'">
    <p class="q-mb-none q-ml-md text-weight-medium text-h6">Finished Drills</p>
    <p class="q-ml-md text-grey-10">
      Listed on the table are the drills that you've finished
    </p>
    <q-table
      ref="drillsTable"
      flat
      class="my-sticky-column-table"
      :rows="finishedDrillStore.drills.data"
      :columns="columns"
      row-key="name"
      :filter="pagination.search"
      v-model:pagination="pagination"
      :loading="loading"
      @request="getData"
    >
      <template #top-left v-if="user?.user_type == 'admin'">
        <div>
          <q-btn
            @click.prevent="addModal = true"
            flat
            icon="mdi-plus-circle-outline"
            color="yellow-9"
            unelevated
            label="Add Drill"
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
        </div>
      </template>
      <template #body-cell-drill="props">
        <q-td :props="props" class="ellipsis" style="max-width: 200px">
          {{ props.row.drill.drill }}
        </q-td>
      </template>
      <template #body-cell-description="props">
        <q-td :props="props" class="ellipsis" style="max-width: 180px">
          {{ props.row.drill.description }}
        </q-td>
      </template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <div
            class="flex no-wrap q-gutter-sm items-center"
            style="margin-top: -5px; padding-left: 15px; padding-right: 15px"
          >
            <q-btn
              @click.prevent="
                viewModal = true;
                selectedDrill = JSON.parse(JSON.stringify(props.row));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-eye"
            />
            <q-btn
              v-if="
                user?.position == 'Coach' || user?.position == 'Assistant-Coach'
              "
              @click.prevent="
                updateModal = true;
                selectedDrill = JSON.parse(JSON.stringify(props.row));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-pencil-outline"
            />
            <q-btn
              v-if="
                user?.position != 'Coach'
              "
              @click.prevent="
                confirmDelete = true;
                selectedDrill = props.row;
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

  <q-dialog v-model="viewModal" persistent>
    <q-card style="min-width: 300px; max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          {{ selectedDrill.drill }}
        </p>
      </q-card-section>

      <q-card-section
        class="q-pt-none q-mt-none"
        style="max-height: 300px; overflow-y: auto"
      >
        <p class="q-mb-none q-mt-sm">
          {{ selectedDrill.description }}
        </p>
        <p class="q-mb-none q-mt-sm text-weight-medium">
          Category: {{ selectedDrill.category.category }}
        </p>
        <p class="text-weight-medium q-mb-none q-mt-sm">Duration:</p>
        <div class="flex items-center no-wrap">
          <p class="q-mb-sm">Hours: {{ selectedDrill.hours }}</p>
          ,&nbsp;
          <p class="q-mb-sm">Minutes: {{ selectedDrill.minutes }}</p>
          ,&nbsp;
          <p class="q-mb-sm">Seconds: {{ selectedDrill.seconds }}</p>
        </div>
        <p class="text-weight-medium">Instructions:</p>
        <p v-html="selectedDrill.instructions"></p>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          flat
          label="Close"
          color="primary"
          v-close-popup
          style="font-size: 0.8rem"
          :disable="isBtnLoading"
        />
        <q-btn
          v-if="user?.position != 'Coach'"
          flat
          label="Mark as Finished"
          color="green"
          v-close-popup
          style="font-size: 0.8rem"
          @click.prevent="markAsFinish"
          :disable="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <q-dialog v-model="confirmDelete" persistent>
    <q-card style="max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Confirm Delete
        </p>
        <p class="q-mb-none q-mt-sm">
          Are you sure you want to delete this finished drill?
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
          @click.prevent="deleteDrill"
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
import { useFinishedDrillsStore } from "../../stores/finished-drills.js";
import { onBeforeMount, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../../composable/useServerPaginate";
import { useCategoryStore } from "src/stores/category";
import { useAuthStore } from "../../stores/authentication";

const columns = [
  {
    name: "drill",
    label: "Drill",
    align: "left",
    field: (row) => row.drill.description,
    sortable: false,
  },
  {
    name: "description",
    label: "Description",
    align: "left",
    field: (row) => row.drill.description,
    sortable: false,
  },
  {
    name: "category",
    label: "Category",
    align: "left",
    field: (row) => row.drill.category?.category ?? "Uncategorized",
    sortable: false,
  },
  {
    name: "finished_on",
    label: "Finished On",
    align: "left",
    field: (row) => row.finished_on,
    sortable: false,
  },
  {
    name: "created_on",
    label: "Created On",
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

const selectedDrill = ref({ instructions: "", drill: "", description: "" });
const confirmDelete = ref(false);
const viewModal = ref(false);
const loading = ref(false);
const categories = ref(false);
const drillsTable = ref("");

let { user, authUser } = useAuthStore();
let { pagination } = useServerPaginate();
const finishedDrillStore = useFinishedDrillsStore();
const { getAll } = useCategoryStore();

const isBtnLoading = ref(false);
const toast = useToast();

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);

onBeforeMount(async () => {
  const { data } = await authUser();
  user = data.data.user;
});

onMounted(() => {
  console.log("Auth User: ", user);
});

onBeforeMount(async () => {
  await getData();
  await getCategories();
});

const getCategories = async () => {
  const { status, data } = await getAll();
  categories.value = data;
};

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await finishedDrillStore.get(initialPage);

  if (status == 200) {
    finishedDrillStore.drills = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const deleteDrill = async () => {
  isBtnLoading.value = true;
  const { data, status } = await finishedDrillStore.deleteDrill(selectedDrill.value.id);

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
