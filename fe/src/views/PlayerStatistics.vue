<template>
  <div class="q-my-md">
    <q-table
      flat
      ref="statisticsTable"
      class="my-sticky-column-table"
      :rows="statisticsStore.statistics.data"
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
      <template
        #top-left
        v-if="user?.user_type == 'admin' || user.position == 'Assistant-Coach'"
      >
        <div>
          <q-btn
            flat
            @click.prevent="addModal = true"
            icon="mdi-table-large-plus"
            color="yellow-9"
            unelevated
            label="New Data"
            style="font-size: 0.85rem"
          />
        </div>
      </template>
      <template #top-right>
        <div class="flex items-center">
          <div style="width: 240px">
            <q-select
              clearable
              dense
              v-model="pagination.school_year"
              :options="schoolYear"
              label="School Year"
              class="q-mr-md"
            />
          </div>
          <div style="width: 240px">
            <q-input
              dense
              debounce="300"
              v-model="pagination.search"
              hide-bottom-space
              placeholder="Search"
            >
              <template #append>
                <q-icon name="search" />
              </template>
            </q-input>
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
              v-if="
                user?.user_type == 'admin' || user.position == 'Assistant-Coach'
              "
              @click.prevent="
                setSelectedStatistics(props.row);
                viewResults = true;
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-eye"
            />
            <q-btn
              v-if="
                user?.user_type == 'admin' || user.position == 'Assistant-Coach'
              "
              @click.prevent="
                updateModal = true;
                selectedStatistics = JSON.parse(JSON.stringify(props.row));
              "
              flat
              size="10px"
              round
              color="grey-7"
              icon="mdi-pencil-outline"
            />
            <q-btn
              v-if="
                user?.user_type == 'admin' || user.position == 'Assistant-Coach'
              "
              @click.prevent="
                confirmDelete = true;
                selectedStatistics = props.row;
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

  <q-dialog v-model="viewResults" persistent>
    <q-card style="min-width: 480px; max-width: 480px">
      <q-card-section class="">
        <p class="q-mb-none text-weight-medium" style="font-size: 1.1rem">
          Calculated Results
        </p>
        <p class="q-mb-none q-mt-sm">
          Here are the calculated results based on the given data.
        </p>
        <div class="q-my-lg">
          <BarChart :series="series" :options="chartOptions" />
        </div>
        <div class="row q-gutter-lg">
          <div>
            <p class="q-mb-none">
              Free Throws Score: {{ selectedStatistics.data.free_throws }}
            </p>
            <p class="q-mb-none">
              Field Goals Score: {{ selectedStatistics.data.field_goals }}
            </p>
            <p class="q-mb-none">
              Three Points Score: {{ selectedStatistics.data.three_points }}
            </p>
            <p class="q-mb-none">
              Total Shot Attempts:
              {{
                totalShotAttempts(
                  selectedStatistics.free_throws_attempted,
                  selectedStatistics.field_goals_attempted,
                  selectedStatistics.three_pointers_attempted
                )
              }}
            </p>
            <p class="q-mb-none">
              Total Scores:
              {{
                selectedStatistics.data.three_points +
                selectedStatistics.data.field_goals +
                selectedStatistics.data.free_throws
              }}
            </p>
          </div>
          <div>
            <p class="q-mb-none">
              Offensive Rebound:
              {{ selectedStatistics.offensive_rebound }}
            </p>
            <p class="q-mb-none">
              Defensive Rebound:
              {{ selectedStatistics.defensive_rebound }}
            </p>
            <p class="q-mb-none">
              Total Blocks: {{ selectedStatistics.total_block }}
            </p>
            <p class="q-mb-none">
              Total Steal {{ selectedStatistics.total_steal }}
            </p>
            <p class="q-mb-none">
              Total Assists {{ selectedStatistics.total_assist }}
            </p>
            <p class="q-mb-none">
              Three-Point Attempts:
              {{ selectedStatistics.three_pointers_attempted }}
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
          Are you sure you want to delete this data?
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
          @click.prevent="deleteStatistics"
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
          Update Data
        </p>
        <p class="q-mb-none">All fields are required</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="max-height: 400px">
        <q-form ref="form" @submit="updateStatistics" class="q-mt-md">
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.free_throws_attempted"
                label="FT Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.free_throws_made"
                label="FT Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.field_goals_attempted"
                label="FG Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.field_goals_made"
                label="FG Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.three_pointers_attempted"
                label="3P Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.three_pointers_made"
                label="3P Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.total_block"
                label="Total Blocks"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.offensive_rebound"
                label="Offensive Rebound"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.defensive_rebound"
                label="Defensive Rebound"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.total_assist"
                label="Total Assists"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="selectedStatistics.total_steal"
                label="Total Steals"
              />
            </div>
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
          Add Data
        </p>
        <p class="q-mb-none">Please fill-in all the fields for the schedule</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="max-height: 400px">
        <q-form ref="saveForm" @submit="saveSchedule" class="q-mt-md">
          <q-select
            outlined
            dense
            v-model="playerStatistics.user_id"
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
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.free_throws_attempted"
                label="FT Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.free_throws_made"
                label="FT Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.field_goals_attempted"
                label="FG Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.field_goals_made"
                label="FG Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.three_pointers_attempted"
                label="3P Attempted"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.three_pointers_made"
                label="3P Made"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.total_block"
                label="Total Blocks"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.offensive_rebound"
                label="Offensive Rebound"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.defensive_rebound"
                label="Defensive Rebound"
              />
            </div>
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.total_assist"
                label="Total Assists"
              />
            </div>
          </div>
          <div class="row q-gutter-sm">
            <div class="col">
              <q-input
                outlined
                type="number"
                :rules="[required]"
                hide-bottom-space
                class="q-mt-sm"
                dense
                v-model="playerStatistics.total_steal"
                label="Total Steal"
              />
            </div>
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
import { useStatisticsStore } from "src/stores/player-statistics";
import { onBeforeMount, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";
import { useAuthStore } from "src/stores/authentication";
import { useAthleteStore } from "src/stores/athletes";
import BarChart from "src/components/BarChart.vue";

const columns = [
  {
    name: "name",
    label: "Name",
    align: "left",
    field: (row) => row.user?.first_name + " " + row.user?.last_name,
    sortable: false,
  },
  {
    name: "ft_attempted",
    label: "FT Attempted",
    align: "left",
    field: (row) => row.free_throws_attempted,
    sortable: false,
  },
  {
    name: "ft_made",
    label: "FT Made",
    align: "left",
    field: (row) => row.free_throws_made,
    sortable: false,
  },
  {
    name: "fg_attempted",
    label: "FG Attempted",
    align: "left",
    field: (row) => row.field_goals_attempted,
    sortable: false,
  },
  {
    name: "fg_made",
    label: "FG Made",
    align: "left",
    field: (row) => row.field_goals_made,
    sortable: false,
  },
  {
    name: "tp_attempted",
    label: "3P Attempted",
    align: "left",
    field: (row) => row.three_pointers_attempted,
    sortable: false,
  },
  {
    name: "tp_made",
    label: "3P Made",
    align: "left",
    field: (row) => row.three_pointers_made,
    sortable: false,
  },
  {
    name: "total_blocks",
    label: "Blocks",
    align: "left",
    field: (row) => row.total_block,
    sortable: false,
  },
  {
    name: "total_steal",
    label: "Steals",
    align: "left",
    field: (row) => row.total_steal,
    sortable: false,
  },
  {
    name: "total_assists",
    label: "Blocks",
    align: "left",
    field: (row) => row.total_assist,
    sortable: false,
  },
  {
    name: "offensive_rebound",
    label: "Offensive Rebounds",
    align: "left",
    field: (row) => row.offensive_rebound,
    sortable: false,
  },
  {
    name: "defensive_rebound",
    label: "Defensive Rebounds",
    align: "left",
    field: (row) => row.defensive_rebound,
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

const playerStatistics = ref({
  free_throws_attempted: "",
  free_throws_made: "",
  field_goals_attempted: "",
  field_goals_made: "",
  three_pointers_attempted: "",
  three_pointers_made: "",
});

const selectedStatistics = ref({ data: {} });
const confirmDelete = ref(false);
const viewResults = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);
const { user } = useAuthStore();
const { athleteOptions } = useAthleteStore();
const series = ref([]);
const chartOptions = ref({
  chart: {
    height: 300,
    width: "100%",
    type: "bar",
  },
  stroke: {
    colors: ["transparent"],
    width: 3,
  },
  plotOptions: {
    bar: {
      borderRadius: 5,
      dataLabels: {
        position: "top", // top, center, bottom
      },
      columnWidth: "40%",
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val + "%";
    },
    offsetY: -20,
    style: {
      fontSize: "12px",
      colors: ["#304758"],
    },
  },
  xaxis: {
    categories: [],
    position: "bottom",
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
    crosshairs: {
      fill: {
        type: "gradient",
        gradient: {
          colorFrom: "#D8E3F0",
          colorTo: "#BED1E6",
          stops: [0, 100],
          opacityFrom: 0.4,
          opacityTo: 0.5,
        },
      },
    },
    tooltip: {
      enabled: true,
    },
  },
  yaxis: {
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
    labels: {
      show: false,
      formatter: function (val) {
        return val + "%";
      },
    },
  },
  title: {
    text: "Player Statistics",
    floating: true,
    offsetY: 0,
    align: "center",
    style: {
      color: "#444",
    },
  },
});

let { pagination, schoolYear } = useServerPaginate();
const { required } = useFieldRules();
const statisticsStore = useStatisticsStore();

const statisticsTable = ref("");
const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");
const athletes = ref([]);

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

watch(
  () => pagination.value.school_year,
  async () => {
    statisticsTable.value.requestServerInteraction();
  }
);

const setSelectedStatistics = (data) => {
  selectedStatistics.value = { data: {} };

  selectedStatistics.value = data;
  selectedStatistics.value.data = {
    free_throws: 0,
    field_goals: 0,
    three_points: 0,
    total_score: 0,
  };

  selectedStatistics.value.data.free_throws =
    parseInt(selectedStatistics.value.free_throws_made) * 1;
  selectedStatistics.value.data.field_goals =
    parseInt(selectedStatistics.value.field_goals_made) * 2;
  selectedStatistics.value.data.three_points =
    parseInt(selectedStatistics.value.three_pointers_made) * 3;
  selectedStatistics.value.data.total_score =
    selectedStatistics.value.data.free_throws +
    selectedStatistics.value.three_points +
    selectedStatistics.value.free_throws;

  selectedStatistics.value.chart = {
    categories: ["FT ", "FG ", "3P ", "FS", "Shooting"],
    data: [
      (
        (parseInt(selectedStatistics.value.free_throws_made) /
          parseInt(selectedStatistics.value.free_throws_attempted)) *
        100
      ).toFixed(2),
      (
        (parseInt(selectedStatistics.value.field_goals_made) /
          parseInt(selectedStatistics.value.field_goals_attempted)) *
        100
      ).toFixed(2),
      (
        (parseInt(selectedStatistics.value.three_pointers_made) /
          parseInt(selectedStatistics.value.three_pointers_attempted)) *
        100
      ).toFixed(2),
      (
        (parseInt(selectedStatistics.value.field_goals_made) +
          parseInt(selectedStatistics.value.three_pointers_made)) /
          parseInt(selectedStatistics.value.field_goals_attempted) +
        parseInt(selectedStatistics.value.three_pointers_attempted)
      ).toFixed(2),
      (
        (parseInt(selectedStatistics.value.free_throws_made) +
          parseInt(selectedStatistics.value.field_goals_made) +
          parseInt(selectedStatistics.value.three_pointers_made)) /
          parseInt(selectedStatistics.value.free_throws_attempted) +
        (parseInt(selectedStatistics.value.field_goals_attempted) +
          parseInt(selectedStatistics.value.three_pointers_attempted))
      ).toFixed(2),
    ],
  };

  chartOptions.value.xaxis.categories =
    selectedStatistics.value.chart.categories;

  series.value = [];
  series.value.push({
    name: "Player Statistics",
    data: selectedStatistics.value.chart.data,
  });
};

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await statisticsStore.get(initialPage);

  if (status == 200) {
    statisticsStore.statistics = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveSchedule = async () => {
  isBtnLoading.value = true;

  const { data, status } = await statisticsStore.create(playerStatistics.value);
  toast.success(data.msg);

  if (status === 200) {
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateStatistics = async () => {
  isBtnLoading.value = true;

  const { data, status } = await statisticsStore.update(
    selectedStatistics.value
  );

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteStatistics = async () => {
  isBtnLoading.value = true;
  const { data, status } = await statisticsStore.deleteStatistics(
    selectedStatistics.value.id
  );

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};

const totalShotAttempts = (a1, a2, a3) =>
  parseInt(a1) + parseInt(a2) + parseInt(a3);
const totalFieldAttempts = (a1, a2) => parseInt(a1) + parseInt(a2);
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
