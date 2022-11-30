<template>
  <div>
    <p>Athlete Performance Page</p>
    <q-table :rows="rows" :columns="columns" row-key="name" />
  </div>
</template>
<script setup>
import { useAthleteStore } from "../stores/athletes.js";
import { onBeforeMount, ref } from "vue";
import { useToast } from "vue-toastification";

const userData = ref({ position: "", first_name: "", last_name: "" });
const userID = ref(null);

const columns = [
  {
    name: "athlete",
    required: true,
    label: "Athlete",
    align: "left",
    field: (row) => row.first_name + ' ' + row.last_name,
    sortable: true,
  },
  {
    name: "email",
    required: true,
    label: "Email Address",
    align: "left",
    field: (row) => row.email,
    sortable: true,
  },
  {
    name: "position",
    required: true,
    label: "Position",
    align: "left",
    field: (row) => row.position,
    sortable: true,
  },
  {
    name: "address",
    required: true,
    label: "Address",
    align: "left",
    field: (row) => row.address,
    sortable: true,
  },
  {
    name: "created_on",
    required: true,
    label: "Created On",
    align: "left",
    field: (row) => row.created_at,
    sortable: true,
  },
];

const athleteStore = useAthleteStore();
const isCreateModalShown = ref(false);
const isDeleteModalShown = ref(false);
const isBtnLoading = ref(false);
const toast = useToast();
const form = ref("");

const toggleCreateModal = () =>
  (isCreateModalShown.value = !isCreateModalShown.value);

const toggleDeleteModal = () =>
  (isDeleteModalShown.value = !isDeleteModalShown.value);

onBeforeMount(async () => {
  const { status, data } = await athleteStore.get();
  if (status == 200) {
    athleteStore.athletes = data;
  }
});

const getData = async () => {
  const { status, data } = await athleteStore.get();
  if (status == 200) {
    athleteStore.athletes = data;
  }
};

const saveAccount = async () => {
  isBtnLoading.value = true;
  const { data, status } = await athleteStore.create(userData.value);
  toast.success(data.msg);
  if (status === 200) {
    form.value.reset();
    await getData();
  }
  isBtnLoading.value = false;
  toggleCreateModal();
};

const deleteUser = async () => {
  isBtnLoading.value = true;
  const { data, status } = await athleteStore.deleteAthlete(userID.value);
  if (status === 200) {
    toast.success(data.msg);
    await getData();
  }

  isBtnLoading.value = false;
  toggleDeleteModal();
};
</script>
