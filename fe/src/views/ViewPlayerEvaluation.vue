<template>
  <div class="q-mt-md q-mb-lg">
    <div class="q-pl-md q-mt-lg">
      <q-btn
        unelevated
        to="/player-evaluation"
        color="yellow-9"
        label="Go Back"
      />
    </div>
    <div class="col q-pa-md">
      <div class="row q-mt-lg q-mb-md" style="font-size: 1.2rem">
        <div class="q-mr-lg">
          <p class="q-mb-none text-weight-medium">Athlete:</p>
          <p>
            {{
              perfEvalStore.athletePerformance?.performance?.user?.first_name
            }}
            {{ perfEvalStore.athletePerformance?.performance?.user?.last_name }}
          </p>
        </div>
        <div class="q-mr-lg">
          <p class="q-mb-none text-weight-medium">Height:</p>
          <p>
            {{ perfEvalStore.athletePerformance?.performance?.user?.height }}cm
          </p>
        </div>
        <div class="q-mr-lg">
          <p class="q-mb-none text-weight-medium">Weight:</p>
          <p>
            {{ perfEvalStore.athletePerformance?.performance?.user?.weight }}kg
          </p>
        </div>
      </div>
      <q-separator />
      <div class="row justify-between q-mt-lg">
        <div>
          <p
            class="q-mb-none q-pb-none"
            style="font-size: 1.1rem; font-weight: 600"
          >
            Athlete's Evaluation
          </p>
          <p class="q-mb-none q-pb-none">
            Here is the evaluation data for this athlete
          </p>
        </div>
        <div>
          <p>
            <span class="text-weight-medium">Evaluation Date:</span>
            {{ perfEvalStore.athletePerformance?.performance?.created_at }}
          </p>
        </div>
      </div>
      <div class="row q-my-md">
        <BarChart :options="chartOptions" :series="series" />
      </div>
    </div>
    <q-form ref="form">
      <div class="row wrap q-mt-md q-pb-lg q-pa-md" style="">
        <template
          v-for="(evaluation, i) in perfEvalStore.athletePerformance.data"
          :key="i"
        >
          <div class="col-xs-11 col-sm-5 col-md-5 col-lg-3">
            <div class="row justify-between items-center q-mb-md q-mt-lg">
              <p class="q-mb-none text-weight-bold text-uppercase">
                {{ evaluation.evaluation }}
              </p>
              <p
                class="q-mb-none q-mr-md text-weight-bold"
                :class="getClassColor(evaluation.percentage)"
              >
                {{ evaluation.percentage }}%
              </p>
            </div>
            <template v-for="categ in evaluation.category" :key="categ.id">
              <div
                class="flex no-wrap items-center q-gutter-md justify-between q-pr-lg"
              >
                <p class="q-mb-none">{{ categ.category }}</p>
                <q-input
                  :model-value="categ.score.score"
                  hide-bottom-space
                  dense
                  readonly
                  type="number"
                  step="any"
                  label="Score"
                  :rules="[
                    (v) =>
                      parseInt(v) <= 5 || 'Score must not be greater than 5',
                  ]"
                />
              </div>
            </template>
          </div>
        </template>
      </div>
      <q-input
        dense
        outlined
        type="textarea"
        readonly
        autogrow
        :model-value="perfEvalStore.athletePerformance?.performance?.comment"
        label="Comment"
        :rules="[
          (v) =>
            v.length > 5 ||
            'Please add a comment for this athlete\'s performance',
        ]"
      />
    </q-form>
  </div>
</template>
<script setup>
import { usePerfEvalStore } from "src/stores/performance-evaluation";
import BarChart from "../components/BarChart.vue";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const perfEvalStore = usePerfEvalStore();
const form = ref("");
const routeIDParam = ref(null);
const series = ref([]);
const chartOptions = ref({
  chart: {
    height: 300,
    width: "100%",
    type: "bar",
  },
  stroke: {
    colors: ["transparent"],
    width: 10,
  },
  plotOptions: {
    bar: {
      borderRadius: 10,
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
      show: true,
      formatter: function (val) {
        return val + "%";
      },
    },
  },
  title: {
    text: "Player Performance Evaluation",
    floating: true,
    offsetY: 0,
    align: "center",
    style: {
      color: "#444",
    },
  },
});

onMounted(async () => {
  routeIDParam.value = route.params.id ?? null;

  await getPerformanceData();
  calcPercentages();

  setChartData();
});

const setChartData = () => {
  if (perfEvalStore.athletePerformance.data.length > 0) {
    perfEvalStore.athletePerformance?.data.map((perf) => {
      series.value.push({ name: perf.evaluation, data: [] });
    });
  }

  perfEvalStore.athletePerformance?.data.map((performance, i) => {
    chartOptions.value.xaxis.categories.push(performance.evaluation);
    series.value[i].data.push(performance.percentage);
  });
};

const getPerformanceData = async () => {
  const { data } = await perfEvalStore.getPerformance(routeIDParam.value);
  perfEvalStore.athletePerformance = data;
};

const calcPercentages = () => {
  perfEvalStore.athletePerformance.data.map((evaluation) => {
    let total = 0,
      counter = 0;
    evaluation.category.map((categ) => {
      total += parseInt(categ.score?.score);
      counter++;
    });

    evaluation.percentage = ((total / (counter * 5)) * 100).toFixed(2);
  });
};

const getClassColor = (percent) => {
  if (percent < 75) {
    return "text-red-5";
  } else if (percent < 85) {
    return "text-yellow-9";
  } else {
    return "text-green-9";
  }
};
</script>
