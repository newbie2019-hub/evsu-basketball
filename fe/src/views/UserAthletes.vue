<template>
  <div class="q-my-md">
    <q-table
      flat
      ref="athleteTable"
      class="my-sticky-column-table"
      :rows="athleteStore.athletes.data"
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
            flat
            @click.prevent="addModal = true"
            icon="mdi-account-circle-outline"
            color="yellow-9"
            unelevated
            label="Add Athlete"
            style="font-size: 0.85rem"
          />
          <q-toggle
            v-if="user.position == 'Assistant-Coach'"
            v-model="pagination.assignedPlayers"
            @update:model-value="filterAthleteTeam"
            true-value="Yes"
            false-value=""
            label="Assigned Players Only"
          />
        </div>
      </template>
      <template #top-right>
        <div class="flex items-center q-gutter-sm">
          <q-select
            dense
            v-model="pagination.filter_by_team"
            use-input
            input-debounce="300"
            label="Filter Team"
            :options="allTeams"
            option-value="id"
            :option-label="(item) => item.team"
            @filter="filterFn"
            emit-value
            map-options
            @update:model-value="filterAthleteTeam"
          >
            <template v-slot:append>
              <q-icon
                size="16px"
                v-if="pagination.filter_by_team == ''"
                class="cursor-pointer"
                name="clear"
                @click.stop.prevent="pagination.filter_by_team = null"
              />
            </template>
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey"> No results </q-item-section>
              </q-item>
            </template>
          </q-select>
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
              v-if="user.position == 'Coach' && !props.row.approved_at"
              @click.prevent="
                toggleApproveModal();
                setSelectedAthlete(JSON.parse(JSON.stringify(props.row)));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-check"
            />
            <q-btn
              v-if="user.position == 'Coach' && !props.row.declined_at"
              @click.prevent="
                toggleDeclineModal();
                setSelectedAthlete(JSON.parse(JSON.stringify(props.row)));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-close"
            />
            <q-btn
              v-if="allowAssignDrill(props.row.id)"
              @click.prevent="
                assignDrills = true;
                setSelectedAthlete(JSON.parse(JSON.stringify(props.row)));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-basketball"
            />
            <q-btn
              :to="`/athletes/${props.row.id}`"
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-eye"
            />
            <q-btn
              @click.prevent="
                updateModal = true;
                selectedAthlete = JSON.parse(JSON.stringify(props.row));
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
                selectedAthlete = props.row;
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
              v-if="selectedAthlete?.photo && selectedAthlete?.photo !== 'null'"
              :src="`http://127.0.0.1:8000/images/profile/${selectedAthlete?.photo}`"
            />
            <img :src="`https://robohash.org/${selectedAthlete?.id}`" />
          </q-avatar>
          <p
            class="q-mb-none q-mt-sm text-weight-medium"
            style="font-size: 1.1rem"
          >
            {{ selectedAthlete?.first_name }} {{ selectedAthlete?.last_name }}
          </p>
          <p class="text-caption ellipsis">{{ selectedAthlete?.email }}</p>
        </div>
        <div class="row">
          <div class="col">
            <p class="q-mb-none">
              <span class="text-weight-bold">Gender:</span>
              {{ selectedAthlete?.gender }}
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Address:</span>
              {{ selectedAthlete?.address }}
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Course:</span>
              {{ selectedAthlete?.course ?? "N/A" }}
            </p>
          </div>
          <div class="col">
            <p class="q-mb-none">
              <span class="text-weight-bold">Height:</span>
              {{ selectedAthlete?.height }} cm
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Weight:</span>
              {{ selectedAthlete?.weight }} kg
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Contact:</span>
              {{ selectedAthlete?.contact ?? "N/A" }}
            </p>
            <p class="q-mb-none">
              <span class="text-weight-bold">Year:</span>
              {{ selectedAthlete?.year ?? "N/A" }}
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

  <q-dialog v-model="approveModal" persistent>
    <q-card style="max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Confirm Approve
        </p>
        <p class="q-mb-none q-mt-sm">
          Are you sure you want to approve this athlete?
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
          @click.prevent="approveAthlete"
          flat
          label="Approve"
          color="green-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>

  <q-dialog v-model="declineModal" persistent>
    <q-card style="max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Confirm Decline
        </p>
        <p class="q-mb-none q-mt-sm">
          Are you sure you want to decline this athlete?
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
          @click.prevent="declineAthlete"
          flat
          label="Decline"
          color="red-5"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
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
          Are you sure you want to delete this athlete? All records for this
          athlete will be deleted as well.
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
          @click.prevent="deleteAthlete"
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
          Update Athlete
        </p>
        <p class="q-mb-none">
          Please fill-in all the fields for the athlete's data
        </p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="form" @submit="updateAthlete" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            disable
            v-model="selectedAthlete.email"
            label="Email Address"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.first_name"
            label="First Name"
          />
          <q-input
            outlined
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.middle_name"
            label="Middle Name"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.last_name"
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
                v-model="selectedAthlete.height"
                label="Height (cm)"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedAthlete.weight"
                label="Weight (kg)"
              />
            </div>
          </div>
          <q-input
            class="q-mt-sm"
            outlined
            dense
            hide-bottom-space
            v-model="selectedAthlete.date_of_birth"
            mask="date"
            label="Date of Birth"
            :rules="['date']"
          >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="selectedAthlete.date_of_birth">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-select
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.gender"
            :options="genderOptions"
            label="Gender"
          />
          <q-select
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.position"
            :options="posOptions"
            label="Position"
          />
          <q-input
            outlined
            :rules="[required, contactNumber]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedAthlete.contact"
            label="Contact Number"
          />
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            autogrow
            type="textarea"
            v-model="selectedAthlete.address"
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
          Add Athlete
        </p>
        <p class="q-mb-none">
          Please fill-in all the fields for the athlete's data
        </p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="max-height: 400px; overflow-y: auto"
      >
        <q-form ref="saveForm" @submit="saveAthlete" class="q-mt-md">
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
          <q-input
            class="q-mt-sm"
            outlined
            dense
            hide-bottom-space
            v-model="userData.date_of_birth"
            mask="date"
            label="Date of Birth"
            :rules="['date']"
          >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="userData.date_of_birth">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
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
            outlined
            :rules="[required]"
            hide-bottom-space
            class="q-mt-sm"
            dense
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

  <q-dialog v-model="assignDrills" persistent>
    <q-card style="max-width: 380px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Assign Drills
        </p>
        <p class="q-mb-none q-mt-sm">
          Select multiple drills to be assigned for this player
        </p>
        <q-select
          class="q-mt-md"
          outlined
          dense
          v-model="selectedAthlete.drills_id"
          use-input
          input-debounce="300"
          label="Select Drills"
          :options="drills"
          option-value="id"
          :option-label="(item) => item.drill"
          @filter="filterDrills"
          emit-value
          map-options
          multiple
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey"> No results </q-item-section>
            </q-item>
          </template>
        </q-select>
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
          @click.prevent="assignPlayerDrills"
          flat
          label="Assign"
          color="green"
          style="font-size: 0.8rem"
          :loading="isBtnLoading"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script setup>
import { useDrillsStore } from "../stores/drills.js";
import { useAthleteStore } from "../stores/athletes.js";
import { useAuthStore } from "src/stores/authentication";
import { onBeforeMount, ref, toRef, watch } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";
import { useTeamStore } from "src/stores/teams";

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
    name: "team",
    label: "Team",
    align: "left",
    field: (row) => {
      console.log(row?.team?.team?.team);
      return row?.team?.team?.team ?? "No Team";
    },
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
    name: "approved_at",
    label: "Approved At",
    align: "left",
    field: (row) => row.approved_at,
    sortable: false,
  },
  {
    name: "declined_at",
    label: "Declined At",
    align: "left",
    field: (row) => row.declined_at,
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
  position: "",
  first_name: "",
  last_name: "",
  address: "",
  email: "",
  contact: "",
});

let { teamsOption } = useTeamStore();
const { user } = useAuthStore();
const selectedAthlete = ref({});
const confirmDelete = ref(false);
const { drillsOptions } = useDrillsStore();
const addModal = ref(false);
const updateModal = ref(false);
const declineModal = ref(false);
const approveModal = ref(false);
const assignDrills = ref(false);
const loading = ref(false);
const viewModal = ref(false);
const allTeams = ref([]);
const drills = ref([]);

const genderOptions = ["Male", "Female"];
const posOptions = [
  "Point Guard",
  "Small Forward",
  "Power Forward",
  "Shooting Guard",
  "Center",
];

let { pagination } = useServerPaginate();
const { required, minLength, contactNumber } = useFieldRules();
const athleteStore = useAthleteStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");
const athleteTable = ref("");

const toggleCreateModal = () => (addModal.value = !addModal.value);

const toggleDeleteModal = () => (confirmDelete.value = !confirmDelete.value);
const toggleApproveModal = () => (approveModal.value = !approveModal.value);
const toggleDeclineModal = () => (declineModal.value = !declineModal.value);

const submitForm = async () => {
  await form.value.submit();
};

const submitSaveForm = async () => {
  await saveForm.value.submit();
};

onBeforeMount(async () => {
  await getData();
  allowAssignDrill();
});

const filterAthleteTeam = () => {
  athleteTable.value.requestServerInteraction();
};

const filterFn = (val, update) => {
  if (val === "") {
    update(async () => {
      return ["Search"];
    });
  }

  update(async () => {
    const { data } = await teamsOption(val);
    allTeams.value = data;
  });
};

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.sortBy = props?.pagination?.sortBy;
  pagination.value.descending = props?.pagination?.descending;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await athleteStore.get(initialPage);

  if (status == 200) {
    athleteStore.athletes = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveAthlete = async () => {
  isBtnLoading.value = true;

  const { data, status } = await athleteStore.create(userData.value);

  if (status === 200) {
    toast.success(data.msg);
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateAthlete = async () => {
  isBtnLoading.value = true;

  const { data, status } = await athleteStore.update(selectedAthlete.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const approveAthlete = async () => {
  isBtnLoading.value = true;
  const { data, status } = await athleteStore.approve(selectedAthlete.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleApproveModal();
};

const declineAthlete = async () => {
  isBtnLoading.value = true;
  const { data, status } = await athleteStore.decline(selectedAthlete.value);

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeclineModal();
};

const deleteAthlete = async () => {
  isBtnLoading.value = true;
  const { data, status } = await athleteStore.deleteAthlete(
    selectedAthlete.value.id
  );

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};

const allowAssignDrill = (id) => {
  const users = user.players?.map((player) => player.athlete_id);
  if (users.includes(id)) return true;

  return false;
};

const setSelectedAthlete = (data) => {
  selectedAthlete.value = data;
  selectedAthlete.value.drills_id = [];
  data.drills.map((drill) => {
    selectedAthlete.value.drills_id.push(drill.id);
  });
};

const filterDrills = (val, update) => {
  if (val === "") {
    update(async () => {
      return ["Search"];
    });
  }

  update(async () => {
    const { data } = await drillsOptions(val);
    drills.value = data;
  });
};

const assignPlayerDrills = async () => {
  isBtnLoading.value = true;

  const { data, status } = await athleteStore.assignDrills(
    selectedAthlete.value
  );

  if (status === 200) {
    toast.success(data.msg);
    filterAthleteTeam();
  }

  isBtnLoading.value = false;
  assignDrills.value = false;
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
