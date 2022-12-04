<template>
  <div>
    <q-table
      flat
      class="my-sticky-column-table"
      :rows="categoryStore.categories.data"
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
      <template #top-left v-if="user.value.user_type == 'admin'">
        <div>
          <q-btn
            @click.prevent="addModal = true"
            flat
            icon="mdi-table-large-plus"
            color="primary"
            unelevated
            label="New Category"
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
                updateModal = true;
                selectedCategory = JSON.parse(JSON.stringify(props.row));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-pencil-outline"
            />
            <q-btn
              @click.prevent="
                confirmDelete = true;
                selectedCategory = props.row;
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
          Are you sure you want to delete this game schedule?
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
          @click.prevent="deleteSchedule"
          flat
          label="Delete"
          color="red-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <q-dialog v-model="updateModal" id="updateModal">
    <q-card style="min-width: 320px; max-width: 420px">
      <q-card-section class="q-pb-none">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Update Category
        </p>
        <p class="q-mb-none">All fields are required</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="max-height: 400px">
        <q-form ref="form" @submit="updateCategory" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCategory.category"
            label="Category"
          />
        </q-form>
      </q-card-section>

      <q-card-actions align="right" class="q-mt-md">
        <q-btn
          flat
          label="Cancel"
          color="primary"
          v-close-popup
          style="font-size: 0.8rem"
          :disable="isBtnLoading"
        />
        <q-btn
          @click.prevent="submitForm"
          flat
          label="Update"
          color="green-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <q-dialog v-model="addModal" persistent>
    <q-card style="max-width: 400px">
      <q-card-section class="q-pb-none">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Add Schedule
        </p>
        <p class="q-mb-none">Please fill-in all the fields for the schedule</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="max-height: 400px">
        <q-form ref="saveForm" @submit="saveCategory" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="drillCategory.category"
            label="Category"
          />
        </q-form>
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
          @click.prevent="submitSaveForm"
          flat
          label="Save"
          color="green-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script setup>
import { useCategoryStore } from "../../stores/category.js";
import { onBeforeMount, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../../composable/useServerPaginate";
import { useFieldRules } from "../../composable/useFieldRules";
import { useAuthStore } from "src/stores/authentication";
const columns = [
  {
    name: "category",
    label: "Category",
    align: "left",
    field: (row) => row.category,
    sortable: false,
  },
  {
    name: "drills_count",
    label: "No. Drills",
    align: "left",
    field: (row) => row.drills_count,
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

const drillCategory = ref({ category: "" });
const selectedCategory = ref({});
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);
const emits = defineEmits(['categoryUpdated']);

const { user } = useAuthStore()
let { pagination } = useServerPaginate();
const { required, minLength } = useFieldRules();
const categoryStore = useCategoryStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");

const toggleCreateModal = () => (addModal.value = !addModal.value);

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);

const submitForm = async () => {
  await form.value.submit();
};

const submitSaveForm = async () => {
  await saveForm.value.submit();
};

onBeforeMount(async () => {
  await getData();
});

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await categoryStore.get(initialPage);

  if (status == 200) {
    categoryStore.categories = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveCategory = async () => {
  isBtnLoading.value = true;

  const { data, status } = await categoryStore.create(drillCategory.value);
  toast.success(data.msg);

  if (status === 200) {
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateCategory = async () => {
  isBtnLoading.value = true;

  const { data, status } = await categoryStore.update(
    selectedCategory.value
  );

  if (status === 200) {
    toast.success(data.msg);
    categoryStore.updateDillStore()
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteSchedule = async () => {
  isBtnLoading.value = true;
  const { data, status } = await categoryStore.deleteCategory(
    selectedCategory.value.id
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
