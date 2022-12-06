<template>
  <div class="q-my-md">
    <q-table
      flat
      class="my-sticky-column-table"
      :rows="evaluationStore.evaluations.data"
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
      <template #top-left v-if="user?.user_type == 'admin'">
        <div>
          <q-btn
            @click.prevent="addModal = true"
            flat
            icon="mdi-table-large-plus"
            color="primary"
            unelevated
            label="New Evaluation"
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
                updateModal = true;
                selectedEvaluation = JSON.parse(JSON.stringify(props.row));
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
                selectedEvaluation = props.row;
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
          Are you sure you want to delete this evaluation? All of its related
          data will be deleted as well.
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
          @click.prevent="deleteEvaluation"
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
          Update Schedule
        </p>
        <p class="q-mb-none">All fields are required</p>
      </q-card-section>
      <q-card-section class="q-pt-none" style="min-width: 380px; max-height: 400px; overflow-y: auto;">
        <q-form ref="form" @submit="updateEvaluation" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="selectedEvaluation.evaluation"
            label="Evaluation"
          />
          <q-separator class="q-mt-md q-mb-md" />
          <p class="q-mb-none text-weight-medium">Categories</p>
          <template
            v-for="(categ, i) in selectedEvaluation.category"
            :key="`eval-categ-${i + 1}`"
          >
            <q-input
              outlined
              :rules="[required, minLength]"
              hide-bottom-space
              class="q-mt-sm"
              dense
              v-model="selectedEvaluation.category[i].category"
              label="Category"
            >
              <template
                v-if="i > 0 && selectedEvaluation.category.length > 0"
                v-slot:append
              >
                <q-btn
                  flat
                  round
                  size="sm"
                  icon="mdi-close-circle-outline"
                  @click.prevent="removeUpdateCategory(i)"
                />
              </template>
            </q-input>
          </template>
          <div class="row justify-end q-mt-md">
            <q-btn
              @click.prevent="addUpdateCategory"
              unelevated
              no-caps
              color="primary"
              label="Add Category"
              style="font-size: 0.72rem"
            />
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
          New Evaluation
        </p>
        <p class="q-mb-none">Please fill-in all the fields for the schedule</p>
      </q-card-section>
      <q-card-section
        class="q-pt-none"
        style="min-width: 380px; max-height: 400px; overflow-y: auto"
      >
        <q-form ref="saveForm" @submit="saveEvaluation" class="q-mt-md">
          <q-input
            outlined
            :rules="[required, minLength]"
            hide-bottom-space
            class="q-mt-sm"
            dense
            v-model="evaluationData.evaluation"
            label="Evaluation"
          />
          <q-separator class="q-mt-md q-mb-md" />
          <p class="q-mb-none text-weight-medium">Categories</p>
          <template
            v-for="(categ, i) in evaluationData.categories"
            :key="`eval-categ-${i + 1}`"
          >
            <q-input
              outlined
              :rules="[required, minLength]"
              hide-bottom-space
              class="q-mt-sm"
              dense
              v-model="evaluationData.categories[i].category"
              label="Category"
            >
              <template
                v-if="i > 0 && evaluationData.categories.length > 0"
                v-slot:append
              >
                <q-btn
                  flat
                  round
                  size="sm"
                  icon="mdi-close-circle-outline"
                  @click.prevent="removeCategory(i)"
                />
              </template>
            </q-input>
          </template>
          <div class="row justify-end q-mt-md">
            <q-btn
              @click.prevent="addCategory"
              unelevated
              no-caps
              color="primary"
              label="Add Category"
              style="font-size: 0.72rem"
            />
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
import { useEvaluationStore } from "../stores/evaluation.js";
import { onBeforeMount, ref } from "vue";
import { useToast } from "vue-toastification";
import { useServerPaginate } from "../composable/useServerPaginate";
import { useFieldRules } from "../composable/useFieldRules";
import { useAuthStore } from "src/stores/authentication";

const columns = [
  {
    name: "evaluation",
    label: "Evaluation",
    align: "left",
    field: (row) => row.evaluation,
    sortable: false,
  },
  {
    name: "category_count",
    label: "No. Category",
    align: "center",
    field: (row) => row.category_count,
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

const evaluationData = ref({ evaluation: "", categories: [{ category: "" }] });
const selectedEvaluation = ref({});
const confirmDelete = ref(false);
const addModal = ref(false);
const updateModal = ref(false);
const loading = ref(false);

const { user } = useAuthStore()
let { pagination } = useServerPaginate();
const { required, minLength } = useFieldRules();
const evaluationStore = useEvaluationStore();

const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");
const saveForm = ref("");

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

const addCategory = () => {
  evaluationData.value.categories.push({ category: "" });
};

const removeCategory = (index) => {
  evaluationData.value.categories.splice(index, 1);
};

const addUpdateCategory = () => {
  selectedEvaluation.value.category.push({ id: '', category: "" });
};

const removeUpdateCategory = (index) => {
  selectedEvaluation.value.category.splice(index, 1);
};

const getData = async (props) => {
  loading.value = true;

  const initialPage = props ?? 1;
  pagination.value.rowsPerPage = props?.pagination?.rowsPerPage ?? 10;

  const { status, data } = await evaluationStore.get(initialPage);

  if (status == 200) {
    evaluationStore.evaluations = data;
    pagination.value.rowsNumber = data.total;
    pagination.value.page = data.current_page;
    pagination.value.from = data.from;
    pagination.value.to = data.to;
    pagination.value.total = data.total;
    pagination.value.last_page = data.last_page;
  }

  loading.value = false;
};

const saveEvaluation = async () => {
  isBtnLoading.value = true;

  const { data, status } = await evaluationStore.create(evaluationData.value);
  toast.success(data.msg);

  if (status === 200) {
    saveForm.value.reset();
    await getData();
  }

  isBtnLoading.value = false;
  toggleCreateModal();
};

const updateEvaluation = async () => {
  isBtnLoading.value = true;

  const { data, status } = await evaluationStore.update(
    selectedEvaluation.value
  );

  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  updateModal.value = false;
  isBtnLoading.value = false;
};

const deleteEvaluation = async () => {
  isBtnLoading.value = true;
  const { data, status } = await evaluationStore.deleteEvaluation(
    selectedEvaluation.value.id
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
