<template>
  <div class="q-mt-lg q-ml-md">
    <p class="q-mb-none text-weight-bold" style="font-size: 1.2rem">
      Welcome to Dashboard
    </p>
    <p class="text-grey-7">Shown below are your summary of data</p>
    <div class="row items-stretch">
      <div class="col-sm-6 col-md-4 col-lg-4 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Registered Athlete's
            </p>
            <p class="q-mb-none q-mt-sm">
              There are currently
              <span
                class="q-mb-none text-weight-bold"
                style="font-size: 0.8rem"
                >{{ dashboardStore.dashboard?.user ?? "00" }}</span
              >
              data for the registered athlete's.
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/athletes" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-4 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Event Schedules
            </p>
            <p class="q-mb-none q-mt-sm">
              There are currently
              <span
                class="q-mb-none text-weight-bold"
                style="font-size: 0.8rem"
                >{{ dashboardStore.dashboard?.gameSchedule ?? "00" }}</span
              >
              event schedules saved on your data
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/schedules" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-4 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Game Evaluations
            </p>
            <p class="q-mb-none q-mt-sm">
              There are currently
              <span
                class="q-mb-none text-weight-bold"
                style="font-size: 0.8rem"
                >{{ dashboardStore.dashboard?.evaluation ?? "00" }}</span
              >
              game evaluations setup on your records.
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/evaluations" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-4 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Total Drills
            </p>
            <p class="q-mb-none q-mt-sm">
              There are currently
              <span
                class="q-mb-none text-weight-bold"
                style="font-size: 0.8rem"
                >{{ dashboardStore.dashboard?.drills ?? "00" }}</span
              >
              game drills added on your records.
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/drills" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
    </div>
  </div>
</template>
<script setup>
import { useDashboardStore } from "../stores/dashboard";
import { ref, onMounted } from "vue";

const dashboardStore = useDashboardStore();

onMounted(async () => {
  const { status, data } = await dashboardStore.get();
  dashboardStore.dashboard = data;
});
</script>
<style>
.card-actions {
  position: absolute;
  bottom: 0;
  display: flex;
  justify-content: flex-end;
  width: 100%;
}

.card-body {
  padding-bottom: 4rem !important;
}
</style>
