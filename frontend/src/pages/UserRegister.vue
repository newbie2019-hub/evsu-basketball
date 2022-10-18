<template>
  <div class="w-screen h-screen flex justify-center items-center">
    <div class="flex-col bg-white shadow-md px-6 py-8 rounded-md">
      <p class="text-lg font-medium">Register Account</p>
      <p class="text-sm text-gray-500">
        We need some informations before we proceed
      </p>
      <form ref="form" class="mt-4">
        <input-field
          v-model="formData.first_name"
          icon="account-outline"
          id="firstname"
          type="text"
          label="First Name"
        />
        <input-field
          v-model="formData.last_name"
          icon="account-outline"
          id="lastname"
          type="text"
          label="Last Name"
        />
        <input-field
          v-model="formData.email"
          icon="email-outline"
          id="email"
          type="email"
          label="Email Address"
        />
        <input-field
          v-model="formData.password"
          icon="lock-outline"
          id="password"
          type="password"
          label="Password"
        />
        <custom-button
          @click.prevent="regAccount"
          block
          label="Register"
          class="mt-4"
          :loading="isLoading"
        />
      </form>
      <hr class="mt-6" />
      <p class="text-xs mt-7 text-center">
        Already have an account?
        <router-link
          to="/login"
          class="text-xs text-blue-500 hover:text-blue-600"
          >Login</router-link
        >
      </p>
    </div>
  </div>
</template>
<script setup>
import InputField from "../components/forms/InputField.vue";
import CustomButton from "../components/CustomButton.vue";
import { useToast } from "vue-toastification";
import { useAuthStore } from "../stores/authentication";
import { ref } from "vue";

const toast = useToast();

const isLoading = ref(false);
const { register } = useAuthStore();

const form = ref("");
const formData = ref({
  first_name: "",
  last_name: "",
  email: "",
  password: "",
});

const regAccount = async () => {
  isLoading.value = true;
  const { data } = await register(formData.value);
  form.value.reset();
  toast.success(data.msg);
  isLoading.value = false;
};
</script>
