<template>
  <div class="w-screen h-screen flex justify-center items-center">
    <div class="flex-col bg-white shadow-md px-6 py-8 rounded-md">
      <p class="text-lg font-medium">Welcome, User</p>
      <p class="text-sm text-gray-500">
        Please enter your account credentials to proceed
      </p>
      <form class="mt-4">
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
        <div class="flex justify-between ml-1">
          <custom-checkbox id="remember" label="Remember me" />
          <router-link
            to="/forgot-password"
            class="text-xs mt-2 text-blue-500 hover:text-blue-600"
            >Forgot Password?</router-link
          >
        </div>
        <custom-button
          block
          label="Login"
          class="mt-4"
          @click.prevent="loginAccount"
          :loading="isLoading"
        />
      </form>
      <hr class="mt-6" />
      <p class="text-xs mt-7 text-center">
        Don't have an account?
        <router-link
          to="/register"
          class="text-xs text-blue-500 hover:text-blue-600"
          >Create Account</router-link
        >
      </p>
    </div>
  </div>
</template>
<script setup>
import InputField from "../components/forms/InputField.vue";
import CustomButton from "../components/CustomButton.vue";
import Cookies from "js-cookie";
import CustomCheckbox from "../components/forms/CustomCheckbox.vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/authentication";
import { useToast } from "vue-toastification";
import { ref } from "vue";

const toast = useToast();
const router = useRouter();

const { login, user } = useAuthStore();
const isLoading = ref(false);
const formData = ref({ email: "", password: "" });

const loginAccount = async () => {
  isLoading.value = true;
  const { status, data } = await login(formData.value);
  if (status == 200) {
    Cookies.set("access_token", data.data.access_token);
    localStorage.setItem("user_data", JSON.stringify(data.data.user));

    user.value = data.data.user;

    toast.success(data.msg);
    router.push("/");
  }
  isLoading.value = false;
};
</script>
