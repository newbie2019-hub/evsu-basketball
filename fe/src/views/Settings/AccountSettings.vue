<template>
  <div class="q-mb-md">
    <p class="q-mt-md q-mb-none text-weight-medium" style="font-size: 1rem">
      Personal Information
    </p>
    <p class="text-grey-6">Please make sure your informations are correct</p>
    <q-form ref="form" @submit="updateData" class="q-mt-md">
      <div class="fit row wrap q-gutter-sm">
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            dense
            outlined
            v-model="userData.first_name"
            label="First Name"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            dense
            outlined
            v-model="userData.middle_name"
            label="Middle Name"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            dense
            outlined
            v-model="userData.last_name"
            label="Last Name"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            type="number"
            step="any"
            dense
            outlined
            hide-bottom-space
            v-model="userData.height"
            label="Height (cm)"
            :rules="[required]"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            type="number"
            step="any"
            dense
            outlined
            hide-bottom-space
            v-model="userData.weight"
            label="Weight (kg)"
            :rules="[required]"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            dense
            outlined
            hide-bottom-space
            v-model="userData.date_of_birth"
            mask="date"
            :rules="['date']"
          >
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="userData.date_of_birth">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input dense outlined v-model="userData.contact" label="Contact" />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-select
            dense
            outlined
            v-model="userData.gender"
            :options="genderOptions"
            label="Gender"
          />
        </div>
        <div class="col-5 col-sm-3 col-md-3 col-lg-3">
          <q-input
            dense
            outlined
            autogrow
            type="textarea"
            v-model="userData.address"
            label="Address"
          />
        </div>
      </div>
      <div class="q-mt-lg">
        <p class="q-mt-md q-mb-none text-weight-medium" style="font-size: 1rem">
          Profile Picture
        </p>
        <p class="text-grey-6">
          Please make sure to upload an appropriate profile picture
        </p>
        <div class="fit row items-center q-gutter-md">
          <q-avatar size="120px">
            <img v-if="userData.image_preview" :src="userData.image_preview" />
            <img v-else-if="userData.photo && userData.photo !== 'null'" :src="`http://127.0.0.1:8000/images/profile/${userData.photo}`" />
            <img v-else :src="`https://robohash.org/${user.id}`" />
          </q-avatar>
          <input
            id="avatar"
            ref="avatar"
            type="file"
            class="hidden"
            accept="image/*"
            @change="imageSelected"
          />
          <q-btn
            unelevated
            color="primary"
            size="sm"
            label="Upload Photo"
            @click.prevent="openFileExplorer"
          />
          <q-btn
            @click.prevent="removePhoto"
            unelevated
            color="red"
            size="sm"
            label="Remove Photo"
          />
        </div>
      </div>
      <div class="q-mt-xl">
        <p class="q-mb-none text-weight-medium" style="font-size: 1rem">
          Login Credentials
        </p>
        <p class="text-grey-6">Please make sure to save your credentials</p>
        <div class="fit row wrap q-gutter-sm">
          <div class="col-5 col-sm-3 col-md-3 col-lg-3">
            <q-input
              dense
              outlined
              v-model="userData.email"
              label="Email Address"
            />
          </div>
          <div class="col-5 col-sm-3 col-md-4 col-lg-4">
            <div class="flex no-wrap">
              <q-input
                type="password"
                dense
                outlined
                v-model="userData.password"
                label="Password"
                disable
                class="q-border-group-input"
              />
              <q-btn unelevated class="q-border-group-btn" color="primary" label="Update" no-caps/>
            </div>
          </div>
        </div>
      </div>
      <div class="row q-mt-md">
        <q-btn
          @click="submitForm"
          unelevated
          no-caps
          color="primary"
          label="Update Information"
          style="font-size: 0.8rem"
        />
      </div>
    </q-form>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../../stores/authentication";
import { useFieldRules } from "src/composable/useFieldRules";
import { useToast } from "vue-toastification";

const userData = ref({ first_name: "", last_name: "", image: null });
const genderOptions = ["Male", "Female"];
const avatar = ref("");
const form = ref("");
const isLoading = ref(false);
const toast = useToast();

const { required } = useFieldRules();
let { authUser, logout, user, update } = useAuthStore();

onMounted(async () => {
  const { data } = await authUser();
  user = data.data.user;

  userData.value = { ...user };
});

const openFileExplorer = () => avatar.value.click();

const removePhoto = () => {
  userData.value.image_preview = "";
  userData.value.image = "";
  userData.value.photo = "";
};

const submitForm = () => form?.value.submit();

const updateData = async () => {
  isLoading.value = true;

  const { status, data } = await update(userData.value);

  if (status == 200) {
    toast.success(data.msg);
  }

  isLoading.value = false;
};

const imageSelected = (event) => {
  userData.value.image = event.target.files[0];
  userData.value.image_preview = event.target.files[0];

  if (!userData.value.image) {
    return false;
  }

  if (!userData.value.image.type.match("image.*")) {
    return false;
  }

  const reader = new FileReader();

  reader.onload = function (e) {
    userData.value.image_preview = e.target.result;
  };

  reader.readAsDataURL(userData.value.image_preview);
};
</script>
<style>
.q-border-group-input .q-field__inner.relative-position .q-field__control {
  border-top-right-radius: 0px !important;
  border-bottom-right-radius: 0px !important;
}

.q-border-group-btn {
  border-top-left-radius: 0px !important;
  border-bottom-left-radius: 0px !important;
}
</style>
