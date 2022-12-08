<template>
  <div class="q-my-md">
    <q-table
      flat
      class="my-sticky-column-table"
      :rows="teamStore.teams.data"
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
            label="Add Team"
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
      <template #body-cell-players="props">
        <q-td :props="props">
          <div class="flex">
            <p class="q-mb-none" v-for="player in props.row.players" :key="player.id">
              {{player.user.last_name}}, &nbsp;
            </p>
          </div>
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
                showUpdateTeam(JSON.parse(JSON.stringify(props.row)))
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
                selectedTeam = props.row;
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
          Are you sure you want to delete this team? All of its players will be
          unassigned from this team.
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
          @click.prevent="deleteTeam"
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
    <q-card style="min-width: 350px; max-width: 400px">
      <q-card-section class="q-pb-none">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Update Team
        </p>
        <p class="q-mb-none">Update your team's information</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="max-height: 400px">
        <q-form ref="form" @submit="updateTeam" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedTeam.team"
            label="Team Name"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            autogrow
            type="textarea"
            v-model="selectedTeam.description"
            label="Description"
          />
          <q-select
            outlined
            dense
            multiple
            class="q-mt-sm"
            v-model="selectedTeam.user_id"
            use-input
            input-debounce="300"
            label="Select Athlete"
            :options="athletes"
            option-value="id"
            :option-label="(item) => item.first_name + ' ' + item.last_name"
            @filter="filterFn"
            emit-value
            map-options
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey"> No results </q-item-section>
              </q-item>
            </template>
          </q-select>
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
          Add Team
        </p>
        <p class="q-mb-none">All fields are required for saving a team</p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="min-width: 350px; max-height: 400px"
      >
        <q-form ref="saveForm" @submit="saveTeam" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="teamData.team"
            label="Team"
          />
          <q-input
            type="textarea"
            outlined
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="teamData.description"
            label="Description"
          />
          <q-select
            outlined
            dense
            multiple
            class="q-mt-sm"
            v-model="teamData.user_id"
            use-input
            input-debounce="300"
            label="Select Athlete"
            :options="athletes"
            option-value="id"
            :option-label="(item) => item.first_name + ' ' + item.last_name"
            @filter="filterFn"
            emit-value
            map-options
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey"> No results </q-item-section>
              </q-item>
            </template>
          </q-select>
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
import { useTeamStore } from "../stores/teams.js";
import { onBeforeMount, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";
import { useAthleteStore } from "src/stores/athletes";

const columns = [
  {
    name: "team",
    label: "Team",
    align: "left",
    field: (row) => row.team,
    sortable: false,
  },
  {
    name: "players",
    label: "Players",
    align: "left",
    sortable: false,
  },
  {
    name: "players_count",
    label: "No. Players",
    align: "left",
    field: (row) => row.players_count,
    sortable: false,
  },
  {
    name: "description",
    label: "Description",
    align: "left",
    field: (row) => row.description ?? "No Data",
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

const teamData = ref({
  team: "",
  description: "",
  user_id: [],
});

const { athleteOptions } = useAthleteStore();
const selectedTeam = ref({ players: [], user_id: [] });
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);

let { pagination } = useServerPaginate();
const { required, minLength, contactNumber } = useFieldRules();
const teamStore = useTeamStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");
const athletes = ref([]);

const toggleCreateModal = () => (addModal.value = !addModal.value);

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);

const showUpdateTeam = (data) => {
  console.log(data);

  selectedTeam.value = data;
  selectedTeam.value.user_id = [];

  data.players?.map((player) => {
    selectedTeam.value.user_id.push(player.user_id);
  });

  console.log('Selected Team: ', selectedTeam.value)
  updateModal.value = true;
};

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

  const { status, data } = await teamStore.get(initialPage);

  if (status == 200) {
    teamStore.teams = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveTeam = async () => {
  isBtnLoading.value = true;

  const { data, status } = await teamStore.create(teamData.value);

  if (status === 200) {
    toast.success(data.msg);
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateTeam = async () => {
  isBtnLoading.value = true;

  const { data, status } = await teamStore.update(selectedTeam.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteTeam = async () => {
  isBtnLoading.value = true;
  const { data, status } = await teamStore.deleteTeam(selectedTeam.value.id);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};

const filterFn = (val, update) => {
  if (val === "") {
    update(async () => {
      return ["Search"];
    });
  }

  update(async () => {
    const { data } = await athleteOptions(val);
    athletes.value = data;
  });
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
