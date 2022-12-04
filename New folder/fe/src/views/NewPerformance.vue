<template>
  <div class="q-mt-md q-mb-lg">
    <q-form ref="form" @submit="submitData">
      <q-select
        outlined
        dense
        v-model="formData.user_id"
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
      <div class="row wrap q-mt-md q-pb-lg q-pa-md" style="">
        <template
          v-for="(evaluation, i) in evaluationStore.allEvaluations"
          :key="i"
        >
          <div class="col-xs-11 col-sm-5 col-md-5 col-lg-3">
            <p class="text-weight-bold q-mt-md text-uppercase">
              {{ evaluation.evaluation }}
            </p>
            <template v-for="(categ, i) in evaluation.category" :key="categ.id">
              <div
                class="flex no-wrap items-center q-gutter-md justify-between q-pr-lg"
              >
                <p class="q-mb-none">{{ categ.category }}</p>
                <q-input
                  v-model="evaluation.category[i].score"
                  hide-bottom-space
                  dense
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
        v-model="formData.comment"
        label="Comment"
        :rules="[v => v.length > 5 || 'Please add a comment for this athlete\'s performance']"
      />
      <div class="row q-gutter-sm">
        <q-btn
          @click.prevent="submitData"
          class="q-mt-md"
          unelevated
          no-caps
          color="primary"
          label="Save Performance"
          style="font-size: 0.8rem"
        ></q-btn>
        <q-btn
          to="/performance"
          class="q-mt-md"
          unelevated
          no-caps
          color="grey-7"
          label="Cancel"
          style="font-size: 0.8rem"
        ></q-btn>
      </div>
    </q-form>
  </div>
</template>
<script setup>
import { useAthleteStore } from "src/stores/athletes";
import { useEvaluationStore } from "src/stores/evaluation";
import { usePerfEvalStore } from "src/stores/performance-evaluation";
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";
import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()
const evaluationStore = useEvaluationStore();
const perfEvalStore = usePerfEvalStore();
const { athleteOptions } = useAthleteStore();
const formData = ref({ user_id: 2, comment: "" });
const athletes = ref([]);
const form = ref("");

onMounted(async () => {
  await getData();
  await getEvalData();
});

const getData = async () => {
  const { data } = await athleteOptions("");
  athletes.value = data;
};

const getEvalData = async () => {
  const { data } = await evaluationStore.getAll();
  evaluationStore.allEvaluations = data;
  evaluationStore.evaluations = data;
};

const submitData = async () => {
  formData.value.evaluations = { ...evaluationStore.allEvaluations };
  const { status, data } = await perfEvalStore.create(formData.value);

  if(status == 200){
    toast.success(data.msg)
  }

  router.push('/performance')
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
