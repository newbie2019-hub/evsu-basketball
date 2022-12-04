<template>
  <div class="row justify-center items-center window-height">
    <div class="col-11 col-xs-7 col-sm-5 col-md-3">
      <q-card>
        <q-card-section style="padding: 2.4rem 2rem">
          <p class="text-weight-medium q-mb-none" style="font-size: 1.4rem">
            Login Account
          </p>
          <p>Please enter your credentials to proceed</p>
          <q-form @submit="loginAccount" class="q-gutter-md">
            <q-input
              hide-bottom-space
              type="email"
              v-model="formData.email"
              label="Email Address"
              lazy-rules
              :rules="[
                (val) => (val && val.length > 0) || 'Email address is required',
              ]"
            />
            <q-input
              hide-bottom-space
              v-model="formData.password"
              label="Password"
              lazy-rules
              :type="passwordShown ? 'text' : 'password'"
              :rules="[
                (val) =>
                  (val !== null && val !== '') || 'Password Field is required',
              ]"
            >
              <template v-slot:append>
                <q-icon
                  :name="passwordShown ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="passwordShown = !passwordShown"
                />
              </template>
            </q-input>
            <q-checkbox
              v-model="formData.remember_me"
              label="Keep me logged in"
              class="q-pa-none q-ml-sm q-mt-sm"
            />
            <div>
              <q-btn
                unelevated
                label="Login Account"
                type="submit"
                color="primary"
                class="block full-width"
                :loading="isLoading"
              />
            </div>
            <q-separator class="q-mt-md block"></q-separator>
            <p class="text-center">
              Dont have an account?
              <router-link to="/register" style="text-decoration: none;">Sign-up</router-link>
            </p>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>
<script setup>
import Cookies from "js-cookie";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/authentication";
import { useToast } from "vue-toastification";
import { ref } from "vue";
import {} from "quasar";
const toast = useToast();
const router = useRouter();

const { login, user } = useAuthStore();
const isLoading = ref(false);
const passwordShown = ref(false);
const formData = ref({ email: "", password: "", remember_me: false });

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
