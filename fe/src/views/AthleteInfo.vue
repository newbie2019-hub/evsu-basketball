<template>
  <div class="q-py-md">
    <div class="row items-center full-width full-height">
      <div class="col-12 col-sm-6 col-md-6 col-lg-4">
        <q-card flat style="min-width: 320px; max-width: 380px">
          <q-card-section class="">
            <div class="column items-center justify-center">
              <q-avatar size="140px" class="cursor-pointer q-mt-md">
                <img
                  v-if="
                    selectedAthlete?.photo && selectedAthlete?.photo !== 'null'
                  "
                  :src="`http://127.0.0.1:8000/images/profile/${selectedAthlete?.photo}`"
                />
                <img :src="`https://robohash.org/${selectedAthlete?.id}`" />
              </q-avatar>
              <p
                class="q-mb-none q-mt-sm text-weight-medium"
                style="font-size: 1.1rem"
              >
                {{ selectedAthlete?.first_name }}
                {{ selectedAthlete?.last_name }}
              </p>
              <p class="text-caption ellipsis">{{ selectedAthlete?.email }}</p>
            </div>
            <div class="">
              <p class="q-mb-none">
                <span class="text-weight-bold">Gender:</span>
                {{ selectedAthlete?.gender }}
              </p>

              <p class="q-mb-none">
                <span class="text-weight-bold">Course:</span>
                {{ selectedAthlete?.course ?? "N/A" }}
              </p>
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
              <p class="q-mb-none">
                <span class="text-weight-bold">Address:</span>
                {{ selectedAthlete?.address }}
              </p>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>
<script setup>
import { useAthleteStore } from "src/stores/athletes";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const athleteStore = useAthleteStore();
const routeIDParam = ref(null);
const selectedAthlete = ref("");

onMounted(async () => {
  routeIDParam.value = route.params.id ?? null;

  await getAthleteData();
});

const getAthleteData = async () => {
  const { data } = await athleteStore.getAthlete(routeIDParam.value);
  selectedAthlete.value = data;
};
</script>
