<template>
  <div>
    <p class="text-lg font-semibold">Atheletes Management</p>
    <p class="text-sm">Manage your athletes data</p>
    <div class="flex justify-end items-center gap-x-2">
      <custom-button
        id="addBtn"
        label="New Athlete"
        class="h-full"
        @click.prevent="toggleCreateModal"
      />
      <div class="md:w-72">
        <input-field id="search" label="Search Athlete" />
      </div>
    </div>
    <div class="overflow-x-auto mt-6">
      <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
          <tr class="[&>*]:uppercase font-medium text-xs text-gray-500">
            <th scope="col" class="py-3.5 pl-4 pr-3 text-left sm:pl-6">ID</th>
            <th
              scope="col"
              class="py-3.5 pl-4 pr-3 text-left sm:pl-6 whitespace-nowrap"
            >
              Athlete
            </th>
            <th
              scope="col"
              class="py-3.5 pl-4 pr-3 text-left sm:pl-6 whitespace-nowrap"
            >
              Email
            </th>
            <th
              scope="col"
              class="py-3.5 pl-4 pr-3 text-left sm:pl-6 whitespace-nowrap"
            >
              Position
            </th>
            <th
              scope="col"
              class="py-3.5 pl-4 pr-3 text-left sm:pl-6 whitespace-nowrap"
            >
              Address
            </th>
            <th
              scope="col"
              class="py-3.5 pl-4 pr-3 text-left sm:pl-6 whitespace-nowrap"
            >
              Added On
            </th>
            <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6 text-left">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(athlete, i) in athleteStore.athletes.data"
            :key="i"
            class="hover:bg-gray-200"
            :class="{ 'bg-red-100': athlete.deleted_at }"
          >
            <td
              class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.id }}
            </td>
            <td
              class="py-4 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.first_name + " " + athlete.last_name }}
            </td>
            <td
              class="py-4 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.email }}
            </td>
            <td
              class="py-4 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.position }}
            </td>
            <td
              class="py-4 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.address }}
            </td>
            <td
              class="py-4 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap sm:pl-6"
            >
              {{ athlete.created_at }}
            </td>
            <td
              class="relative py-4 pl-3 pr-4 text-sm whitespace-nowrap sm:pr-6"
            >
              <div class="flex gap-x-1">
                <custom-button @click="" color="success" label="Update" />
                <custom-button @click.prevent="" color="error" label="Delete" />
              </div>
            </td>
          </tr>
          <tr v-if="athleteStore.athletes.data?.length == 0">
            <td colspan="6">
              <div
                class="py-4 mx-auto font-medium text-sm text-center text-gray-600"
              >
                No data available ..
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <c-modal v-if="isCreateModalShown" @close="toggleCreateModal">
      <template v-slot:title>
        <p class="text-xl font-bold">New Athlete</p>
        <p class="mt-1 text-sm text-gray-500">
          Please enter all required informations for the athlete
        </p>
      </template>
      <template v-slot:body>
        <form ref="form">
          <input-field
            v-model="userData.first_name"
            id="first_name"
            label="First Name"
          />
          <input-field
            v-model="userData.middle_name"
            id="middle_name"
            label="Middle Name"
          />
          <input-field
            v-model="userData.last_name"
            id="last_name"
            label="Last Name"
          />
          <input-field
            v-model="userData.contact"
            id="contact_number"
            label="Contact Number"
          />
          <input-field
            v-model="userData.address"
            id="address"
            label="Address"
          />
          <input-field
            v-model="userData.email"
            type="email"
            id="email"
            label="Email"
          />
          <c-select v-model="userData.position" class="mt-3">
            <option value="" selected disabled>Select Position</option>
            <option value="Point Guard">Point Guard</option>
            <option value="Small Forward">Small Forward</option>
            <option value="Power Forward">Power Forward</option>
            <option value="Shooting Guard">Shooting Guard</option>
            <option value="Center">Center</option>
          </c-select>
        </form>
      </template>
      <template v-slot:footer>
        <div class="flex gap-x-2">
          <custom-button
            @click.prevent="toggleCreateModal"
            text
            size="sm"
            color="error"
            label="Close"
          />

          <custom-button
            @click.prevent="saveAccount"
            text
            size="sm"
            color="success"
            :loading="isBtnLoading"
            label="Save Account"
          />
        </div>
      </template>
    </c-modal>
  </div>
</template>
<script setup>
import { useAthleteStore } from "@/stores/athletes.js";
import { onBeforeMount, ref } from "vue";
import CustomButton from "../components/CustomButton.vue";
import InputField from "../components/forms/InputField.vue";
import CSelect from "../components/CSelect.vue";
import CModal from "../components/CModal.vue";
import { useToast } from "vue-toastification";

const userData = ref({ position: "", first_name: "", last_name: "" });

const athleteStore = useAthleteStore();
const isCreateModalShown = ref(false);
const isBtnLoading = ref(false);
const toast = useToast();
const form = ref('')

const toggleCreateModal = () =>
  (isCreateModalShown.value = !isCreateModalShown.value);

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
  const { data, status } = await athleteStore.create(userData.value);
  toast.success(data.msg);
  if (status === 200) {
    form.value.reset();
    await getData();
  }
  toggleCreateModal();
};
</script>
