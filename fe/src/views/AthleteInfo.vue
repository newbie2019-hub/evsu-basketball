<template>
  <div class="q-py-lg">
    <div class="row q-gutter-md items-stretch full-width full-height">
      <div class="col-12 col-sm-11 col-md-5 col-lg-4">
        <q-card class="">
          <q-card-section>
            <p class="athlete-name q-mb-sm">
              {{
                selectedAthlete.user?.first_name +
                " " +
                selectedAthlete.user?.last_name
              }}
            </p>
            <p>Playing on Team: {{ selectedAthlete.user?.team?.team.team }}</p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Born:
              <span>{{
                moment(selectedAthlete.user?.date_of_birth).format(
                  "MMMM D, YYYY"
                ) ?? "N/A"
              }}</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Age: <span>{{ age ?? "N/A" }}</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              From: <span>{{ selectedAthlete.user?.address ?? "N/A" }}</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Contact: <span>{{ selectedAthlete.user?.contact ?? "N/A" }}</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Position:
              <span>{{ selectedAthlete.user?.position ?? "N/A" }}</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Height: <span>{{ selectedAthlete.user?.height ?? "N/A" }}cm</span>
            </p>
            <q-separator class="q-my-md"></q-separator>
            <p class="row justify-between">
              Weight: <span>{{ selectedAthlete.user?.weight ?? "N/A" }}kg</span>
            </p>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-11 col-md-6 col-lg-7">
        <div class="full-width">
          <q-card class="full-width full-height q-pa-lg">
            <LineChart :options="chartOptions" :series="series" />
          </q-card>
        </div>
      </div>
    </div>
  </div>
  <div class="">
    <p class="q-mb-none q-ml-md text-weight-medium text-h6">Finished Drills</p>
    <p class="q-ml-md text-grey-10">
      Listed on the table are the drills that you've finished
    </p>
    <q-table
      ref="drillsTable"
      flat
      class="my-sticky-column-table"
      :rows="selectedAthlete.user?.drills"
      :columns="columns"
      row-key="name"
      :filter="search"
      :loading="loading"
    >
      <template #top-right>
        <div class="flex items-center q-gutter-sm">
          <q-input dense debounce="300" v-model="search" placeholder="Search">
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
      <template #body-cell-description="props">
        <q-td :props="props" class="ellipsis" style="max-width: 180px">
          {{ props.row.description }}
        </q-td>
      </template>
    </q-table>
  </div>
</template>
<script setup>
import { useAthleteStore } from "src/stores/athletes";
import { useAuthStore } from "src/stores/authentication";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import moment from "moment";
import LineChart from "src/components/LineChart.vue";

const route = useRoute();
const athleteStore = useAthleteStore();
const routeIDParam = ref(null);
const selectedAthlete = ref("");
const { user } = useAuthStore();

const loading = ref(false);
const age = ref(null);
const series = ref([]);
const search = ref("");
const chartOptions = ref({
  chart: {
    height: 400,
    type: "line",
    zoom: {
      enabled: false,
    },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    curve: "straight",
  },
  title: {
    text: "Player Statistics",
    align: "left",
  },
  grid: {
    row: {
      colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
      opacity: 0.5,
    },
  },
  xaxis: {
    categories: [
      "FT Attempt",
      "FT Made",
      "FG Attempt",
      "FG Made",
      "TP Attempt",
      "TP Made",
    ],
  },
});

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
    field: (row) => row.created_at,
    sortable: false,
  },
];

onMounted(async () => {
  routeIDParam.value = route.params.id ?? null;
  await getAthleteData();

  if (selectedAthlete.value.date_of_birth) {
    age.value = moment().diff(
      selectedAthlete.value.user?.date_of_birth,
      "years"
    );
  }

  setChartData();
});

const getAthleteData = async () => {
  const { data } = await athleteStore.getAthlete(routeIDParam.value);
  selectedAthlete.value = data;
};

const setChartData = () => {
  selectedAthlete.value.performance?.map((performance, i) => {
    series.value.push({
      name: performance.created_at,
      data: [
        performance.field_goals_attempted,
        performance.field_goals_made,
        performance.free_throws_attempted,
        performance.free_throws_made,
        performance.three_pointers_attempted,
        performance.three_pointers_made,
      ],
    });
  });
};
</script>

<style>
.bg-athlete {
  top: 0;
  left: 0;
  width: 100%;
  min-height: 100vh;
  height: 100%;
}

.athlete-name {
  font-size: 3rem;
}
</style>
