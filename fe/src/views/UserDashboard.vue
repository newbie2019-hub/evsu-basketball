<template>
  <div class="q-mt-lg q-ml-md">
    <p class="q-mb-none text-weight-bold" style="font-size: 1.2rem">
      Welcome to Dashboard
    </p>
    <p class="text-grey-7">Shown below are your summary of data</p>
    <div class="row items-stretch q-pb-lg">
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm" v-if="user.position == 'Assistant-Coach'">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.assignedAthletes ?? "00"
              }}</span>
            </p>
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Assigned Athlete's
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/athletes" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.user ?? "00"
              }}</span>
            </p>
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Registered Athlete's
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/athletes" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.gameSchedule ?? "00"
              }}</span>
            </p>
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Total Schedules
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/schedules" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.evaluation ?? "00"
              }}</span>
            </p>
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Game Evaluations
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/evaluations" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.drills ?? "00"
              }}</span>
            </p>
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Basketball Drills
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/drills" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-3 q-mt-sm">
        <q-card unelevated class="my-card q-mr-sm q-mt-sm full-height">
          <q-card-section class="card-body">
            <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
              Total Coaches
            </p>
            <p class="q-mb-none q-mt-sm">
              <span class="q-mb-none text-weight-bold font-count">{{
                dashboardStore.dashboard?.coach ?? "00"
              }}</span>
            </p>
          </q-card-section>
          <q-card-actions class="card-actions">
            <q-btn to="/coaches" flat>View All</q-btn>
          </q-card-actions>
        </q-card>
      </div>
    </div>
    <div class="q-my-md q-mt-lg">
      <p class="q-mb-none text-weight-bold" style="font-size: 1.2rem">
        Event Schedules
      </p>
      <p class="text-grey-7">Shown on the calendar are your schedules</p>
      <calendar-schedule :data="schedules" style="height: 400px" />
    </div>
  </div>
</template>
<script setup>

import { useAuthStore } from "src/stores/authentication";
import { useDashboardStore } from "../stores/dashboard";
import CalendarSchedule from "../components/CalendarSchedule.vue";
import { useGameScheduleStore } from "../stores/gameschedule.js";
import { ref, onMounted } from "vue";

const dashboardStore = useDashboardStore();
const gameScheduleStore = useGameScheduleStore();
const { user } = useAuthStore()
const schedules = ref([]);

onMounted(async () => {
  const { status, data } = await dashboardStore.get();
  dashboardStore.dashboard = data;

  await getSchedules();
});

const getSchedules = async () => {
  const { data } = await gameScheduleStore.getSchedules();
  schedules.value = data;
};
</script>
<style>
.card-actions {
  position: absolute;
  top: 0;
  display: flex;
  justify-content: flex-end;
  width: 100%;
}

.font-count {
  font-size: 2.5rem;
}
</style>
