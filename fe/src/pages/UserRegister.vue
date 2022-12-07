<template>
  <div class="row justify-center items-center window-height bg-hero">
    <div class="col-11 col-xs-7 col-sm-6 col-md-6 col-lg-5">
      <q-card>
        <q-card-section style="padding: 2.4rem 2rem">
          <p
            class="text-weight-medium q-mb-none q-ml-sm"
            style="font-size: 1.4rem"
          >
            Register Account
          </p>
          <p class="q-ml-sm">
            Hi, we need some of your informations to proceed
          </p>
          <q-form @submit="registerAccount" class="q-gutter-md">
            <div class="flex q-gutter-sm">
              <div class="col">
                <q-input
                  dense
                  hide-bottom-space
                  v-model="formData.first_name"
                  label="First Name"
                  lazy-rules
                  :rules="[
                    (val) =>
                      (val && val.length > 0) || 'First Name is required',
                  ]"
                />
              </div>
              <div class="col">
                <q-input
                  dense
                  hide-bottom-space
                  v-model="formData.last_name"
                  label="Last Name"
                  lazy-rules
                  :rules="[
                    (val) =>
                      (val && val.length > 0) || 'First Name is required',
                  ]"
                />
              </div>
            </div>
            <div class="flex q-gutter-sm">
              <div class="col">
                <q-input
                  dense
                  hide-bottom-space
                  v-model="formData.date_of_birth"
                  mask="date"
                  label="Date of Birth"
                  :rules="['date']"
                >
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy
                        cover
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-date v-model="formData.date_of_birth">
                          <div class="row items-center justify-end">
                            <q-btn
                              v-close-popup
                              label="Close"
                              color="primary"
                              flat
                            />
                          </div>
                        </q-date>
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div class="col">
                <q-select
                  dense
                  hide-bottom-space
                  v-model="formData.gender"
                  :options="['Male', 'Female']"
                  label="Gender"
                  lazy-rules
                  :rules="[
                    (val) => (val && val.length > 0) || 'Gender is required',
                  ]"
                />
              </div>
            </div>
            <div class="flex q-gutter-sm">
              <div class="col">
                <q-input
                  dense
                  hide-bottom-space
                  v-model="formData.contact"
                  label="Contact"
                  lazy-rules
                  :rules="[
                    (val) =>
                      (val && val.length > 0) || 'Contact Number is required',
                  ]"
                />
              </div>
              <div class="col">
                <q-input
                  dense
                  hide-bottom-space
                  v-model="formData.address"
                  label="Address"
                  lazy-rules
                  :rules="[
                    (val) => (val && val.length > 0) || 'Address is required',
                  ]"
                />
              </div>
            </div>
            <div class="flex q-gutter-sm">
              <div class="col">
                <q-input
                  dense
                  type="number"
                  hide-bottom-space
                  v-model="formData.height"
                  label="Height (cm)"
                  lazy-rules
                  :rules="[
                    (val) => (val && val.length > 0) || 'Height is required',
                  ]"
                />
              </div>
              <div class="col">
                <q-input
                  dense
                  type="number"
                  hide-bottom-space
                  v-model="formData.weight"
                  label="Weight (kg)"
                  lazy-rules
                  :rules="[
                    (val) => (val && val.length > 0) || 'Weight is required',
                  ]"
                />
              </div>
            </div>
            <div class="col q-gutter-sm">
              <q-input
                dense
                hide-bottom-space
                type="email"
                v-model="formData.email"
                label="Email Address"
                lazy-rules
                :rules="[
                  (val) =>
                    (val && val.length > 0) || 'Email address is required',
                ]"
              />
              <q-input
                dense
                hide-bottom-space
                v-model="formData.password"
                label="Password"
                lazy-rules
                :type="passwordShown ? 'text' : 'password'"
                :rules="[
                  (val) =>
                    (val !== null && val !== '') ||
                    'Password Field is required',
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
            </div>
            <div class="col q-gutter-sm">
              <div>
                <q-btn
                  unelevated
                  label="Register Account"
                  type="submit"
                  color="primary"
                  class="block full-width q-mt-lg"
                  :loading="isLoading"
                />
              </div>
              <q-separator class="q-mt-md block"></q-separator>
              <p class="text-center">
                Already have an account?
                <router-link to="/login" style="text-decoration: none"
                  >Log-in</router-link
                >
              </p>
            </div>
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
const toast = useToast();
const router = useRouter();

const { register, user } = useAuthStore();
const isLoading = ref(false);
const passwordShown = ref(false);
const formData = ref({ email: "", password: "" });

const registerAccount = async () => {
  isLoading.value = true;
  const { status, data } = await register(formData.value);

  if (status == 200) {
    toast.success(data.msg);
    router.push("/login");
  }

  isLoading.value = false;
};
</script>
