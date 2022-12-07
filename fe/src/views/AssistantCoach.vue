<template>
  <div class="q-my-md">
    <q-table
      flat
      ref="athleteTable"
      class="my-sticky-column-table"
      :rows="coachStore.coaches.data"
      :columns="columns"
      row-key="name"
      :filter="pagination.search"
      v-model:pagination="pagination"
      :loading="loading"
      @request="getData"
    >
      <template #top-left>
        <div>
          <q-btn
            @click.prevent="addModal = true"
            flat
            icon="mdi-account-circle-outline"
            color="primary"
            unelevated
            label="Add Coach"
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
                viewModal = true;
                selectedCoach = JSON.parse(JSON.stringify(props.row));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-eye"
            />
            <q-btn
              @click.prevent="
                updateModal = true;
                selectedCoach = JSON.parse(JSON.stringify(props.row));
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
                selectedCoach = props.row;
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
    <q-card style="min-width: 320px; max-width: 380px">
      <q-card-section class="">
        <div class="column items-center justify-center">
          <q-avatar size="80px" class="cursor-pointer q-mt-md">
            <img
              v-if="selectedCoach?.photo && selectedCoach?.photo !== 'null'"
              :src="`http://127.0.0.1:8000/images/profile/${selectedCoach?.photo}`"
            />
            <img :src="`https://robohash.org/${selectedCoach?.id}`" />
          </q-avatar>
          <p
            class="q-mb-none q-mt-sm text-weight-medium"
            style="font-size: 1.1rem"
          >
            {{ selectedCoach?.first_name }} {{ selectedCoach?.last_name }}
          </p>
          <p class="text-caption ellipsis">{{ selectedCoach?.email }}</p>
        </div>
        <div class="row">
          <div class="col">
            <p class="q-mb-none">
              <span class="text-weight-bold">Gender:</span>
              {{ selectedCoach?.gender }}
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Address:</span>
              {{ selectedCoach?.address }}
            </p>
          </div>
          <div class="col">
            <p class="q-mb-none">
              <span class="text-weight-bold">Height:</span>
              {{ selectedCoach?.height }} cm
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Weight:</span>
              {{ selectedCoach?.weight }} kg
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Contact:</span>
              {{ selectedCoach?.contact ?? "N/A" }}
            </p>
          </div>
        </div>
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
          Are you sure you want to delete this assistant coach? All records for this
          coach will be deleted as well.
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
          @click.prevent="deleteCoach"
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
          Update Data
        </p>
        <p class="q-mb-none">
          Please fill-in all the fields for the athlete's data
        </p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="form" @submit="updateCoach" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            disable
            v-model="selectedCoach.email"
            label="First Name"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCoach.first_name"
            label="First Name"
          />
          <q-input
            outlined
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCoach.middle_name"
            label="Middle Name"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCoach.last_name"
            label="Last Name"
          />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                :rules="[required, minLength]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedCoach.height"
                label="Height (cm)"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedCoach.weight"
                label="Weight (kg)"
              />
            </div>
          </div>
          <q-select
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCoach.gender"
            :options="genderOptions"
            label="Gender"
          />
          <q-input
            outlined
            :rules="[required, contactNumber]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedCoach.contact"
            label="Contact Number"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            autogrow
            type="textarea"
            v-model="selectedCoach.address"
            label="Address"
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
          Add Coach
        </p>
        <p class="q-mb-none">
          Please fill-in all the fields for the coach's data
        </p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="saveForm" @submit="saveCoach" class="q-mt-md">
          <q-input
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.email"
            label="Email"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.first_name"
            label="First Name"
          />
          <q-input
            outlined
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.middle_name"
            label="Middle Name"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.last_name"
            label="Last Name"
          />
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                step="any"
                :rules="[required, minLength]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="userData.height"
                label="Height (cm)"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                step="any"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="userData.weight"
                label="Weight (kg)"
              />
            </div>
          </div>
          <q-select
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.gender"
            :options="genderOptions"
            label="Gender"
          />
          <q-select
            type="hidden"
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            disable
            v-model="userData.position"
            :options="posOptions"
            label="Position"
          />
          <q-input
            outlined
            :rules="[required, contactNumber]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="userData.contact"
            label="Contact Number"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            autogrow
            type="textarea"
            v-model="userData.address"
            label="Address"
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
import { useCoachStore } from "../stores/coaches.js";
import { onBeforeMount, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";

const columns = [
  {
    name: "athlete",
    label: "Athlete",
    align: "left",
    field: (row) => row.first_name + " " + row.last_name,
    sortable: false,
  },
  {
    name: "email",
    label: "Email Address",
    align: "left",
    field: (row) => row.email,
    sortable: false,
  },
  {
    name: "height",
    label: "Height",
    align: "left",
    field: (row) => row.height + "cm",
    sortable: false,
  },
  {
    name: "weight",
    label: "Weight",
    align: "left",
    field: (row) => row.weight + "kg",
    sortable: false,
  },
  {
    name: "contact",
    label: "Contact Number",
    align: "left",
    field: (row) => row.contact ?? "No Data",
    sortable: false,
  },
  {
    name: "gender",
    label: "Gender",
    align: "left",
    field: (row) => row.gender,
    sortable: false,
  },
  {
    name: "position",
    label: "Position",
    align: "left",
    field: (row) => row.position,
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

const userData = ref({
  position: "Assistant-Coach",
  first_name: "",
  last_name: "",
  address: "",
  email: "",
  contact: "",
});

const selectedCoach = ref({});
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);
const viewModal = ref(false);

const genderOptions = ["Male", "Female"];
const posOptions = [
  "Assistant-Coach",
];

let { pagination } = useServerPaginate();
const { required, minLength, contactNumber } = useFieldRules();
const coachStore = useCoachStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");
const athleteTable = ref("");

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
  pagination.value.sortBy = props?.pagination?.sortBy;
  pagination.value.descending = props?.pagination?.descending;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await coachStore.get(initialPage);

  if (status == 200) {
    coachStore.coaches = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveCoach = async () => {
  isBtnLoading.value = true;

  const { data, status } = await coachStore.create(userData.value);
  toast.success(data.msg);

  if (status === 200) {
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateCoach = async () => {
  isBtnLoading.value = true;

  const { data, status } = await coachStore.update(selectedCoach.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteCoach = async () => {
  isBtnLoading.value = true;
  const { data, status } = await coachStore.deleteCoach(
    selectedCoach.value.id
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
