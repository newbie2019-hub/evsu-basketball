<template>
  <div class="q-my-md">
    <q-table
      ref="drillsTable"
      flat
      class="my-sticky-column-table"
      :rows="drillStore.drills.data"
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
      <template #top-left v-else>
        <div>
          <q-toggle
            v-if="
              user?.position != 'Assistant-Coach' && user.position != 'Coach'
            "
            v-model="pagination.assignedDrills"
            @update:model-value="filterAssignedDrills"
            true-value="Yes"
            false-value=""
            label="Assigned Drills"
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
          {{ props.row.drill }}
        </q-td>
      </template>
      <template #body-cell-duration="props">
        <q-td :props="props" class="ellipsis" style="max-width: 240px">
          <div class="flex no-wrap items-center">
            <p class="q-mb-none">{{ props.row.hours }} Hours</p>
            ,&nbsp;
            <p class="q-mb-none">{{ props.row.minutes }} Minues</p>
            ,&nbsp;
            <p class="q-mb-none">{{ props.row.seconds }} Seconds</p>
          </div>
        </q-td>
      </template>
      <template #body-cell-description="props">
        <q-td :props="props" class="ellipsis" style="max-width: 180px">
          {{ props.row.description }}
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
                user?.position == 'Coach' || user?.position == 'Assistant-Coach'
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

  <q-dialog v-model="updateModal" persistent>
    <q-card style="max-width: 400px">
      <q-card-section class="q-pb-none">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Update Drill
        </p>
        <p class="q-mb-none">
          Please fill-in all the fields for the athlete's data
        </p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="form" @submit="updateDrill" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedDrill.drill"
            label="Drill"
          />
          <q-input
            outlined
            type="textarea"
            autogrow
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedDrill.description"
            label="Description"
          />
          <q-select
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedDrill.drill_category_id"
            :options="categories"
            option-value="id"
            option-label="category"
            emit-value
            map-options
            label="Category"
          />
          <p class="q-mb-none q-mt-sm text-weight-medium">Instructions</p>
          <q-editor
            v-model="selectedDrill.instructions"
            min-height="5rem"
            class="q-mt-sm"
          />
          <p class="q-mb-none q-mt-sm text-weight-medium">
            Duration - Hours :: Minutes :: Seconds
          </p>
          <div class="flex no-wrap q-gutter-sm">
            <q-input
              outlined
              dense
              hide-bottom-space
              label="HH"
              v-model="selectedDrill.hours"
              :rules="[defaultVal, hoursMaxVal]"
            />
            <q-input
              outlined
              dense
              hide-bottom-space
              label="MM"
              v-model="selectedDrill.minutes"
              :rules="[defaultVal, minMaxVal]"
            />
            <q-input
              outlined
              dense
              hide-bottom-space
              label="SS"
              v-model="selectedDrill.seconds"
              :rules="[defaultVal, secMaxVal]"
            />
          </div>
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
          Add Drills
        </p>
        <p class="q-mb-none">Create new game drills for your players</p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="saveForm" @submit="saveDrill" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="gameDrill.drill"
            label="Drill"
          />
          <q-input
            outlined
            type="textarea"
            autogrow
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="gameDrill.description"
            label="Description"
          />
          <q-select
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="gameDrill.drill_category_id"
            :options="categories"
            option-value="id"
            option-label="category"
            emit-value
            map-options
            label="Category"
          />
          <p class="q-mb-none q-mt-sm text-weight-medium">Instructions</p>
          <q-editor
            v-model="gameDrill.instructions"
            min-height="5rem"
            class="q-mt-sm"
          />
          <p class="q-mb-none q-mt-sm text-weight-medium">
            Duration - Hours :: Minutes :: Seconds
          </p>
          <div class="flex no-wrap q-gutter-sm">
            <q-input
              outlined
              dense
              hide-bottom-space
              label="HH"
              v-model="gameDrill.hours"
              :rules="[defaultVal, hoursMaxVal]"
            />
            <q-input
              outlined
              dense
              hide-bottom-space
              label="MM"
              v-model="gameDrill.minutes"
              :rules="[defaultVal, minMaxVal]"
            />
            <q-input
              outlined
              dense
              hide-bottom-space
              label="SS"
              v-model="gameDrill.seconds"
              :rules="[defaultVal, secMaxVal]"
            />
          </div>
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
import { useDrillsStore } from "../../stores/drills.js";
import { onBeforeMount, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../../composable/useServerPaginate";
import { useFieldRules } from "../../composable/useFieldRules";
import { useCategoryStore } from "src/stores/category";
import { useAuthStore } from "../../stores/authentication";

const columns = [
  {
    name: "drill",
    label: "Drill",
    align: "left",
    field: (row) => row.drill,
    sortable: false,
  },
  {
    name: "description",
    label: "Description",
    align: "left",
    field: (row) => row.description,
    sortable: false,
  },
  {
    name: "category",
    label: "Category",
    align: "left",
    field: (row) => row.category?.category ?? "Uncategorized",
    sortable: false,
  },
  {
    name: "duration",
    label: "Duration",
    align: "left",
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

const gameDrill = ref({
  hours: "00",
  minutes: "00",
  seconds: "00",
  instructions: "",
  drill_category_id: null,
  description: "",
  drill: "",
});

const selectedDrill = ref({ instructions: "", drill: "", description: "" });
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const viewModal = ref(false);
const loading = ref(false);
const categories = ref(false);
const drillsTable = ref("");

let { user, authUser, finishDrill } = useAuthStore();
let { pagination } = useServerPaginate();
const { required, minLength, minMaxVal, secMaxVal, hoursMaxVal, defaultVal } =
  useFieldRules();
const drillStore = useDrillsStore();
const { getAll } = useCategoryStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");

const toggleCreateModal = () => (addModal.value = !addModal.value);

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);

onBeforeMount(async () => {
  const { data } = await authUser();
  user = data.data.user;
});

onMounted(() => {
  console.log("Auth User: ", user);
});

const filterAssignedDrills = () => {
  drillsTable.value.requestServerInteraction();
};

const submitForm = async () => {
  await form.value.submit();
};

const submitSaveForm = async () => {
  await saveForm.value.submit();
};

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

  const { status, data } = await drillStore.get(initialPage);

  if (status == 200) {
    drillStore.drills = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveDrill = async () => {
  if (gameDrill.value.instructions?.trim() == "") {
    return toast.error("Please provide an instruction");
  }

  isBtnLoading.value = true;

  const { data, status } = await drillStore.create(gameDrill.value);
  toast.success(data.msg);

  if (status === 200) {
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateDrill = async () => {
  if (
    selectedDrill.value.instructions?.trim() == "" ||
    selectedDrill.value.instructions == null
  ) {
    return toast.error("Please provide an instruction for this drill");
  }

  isBtnLoading.value = true;

  const { data, status } = await drillStore.update(selectedDrill.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteDrill = async () => {
  isBtnLoading.value = true;
  const { data, status } = await drillStore.deleteDrill(selectedDrill.value.id);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};

const markAsFinish = async() => {
  isBtnLoading.value = true
  const { data, status } = await finishDrill(selectedDrill.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
}
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
