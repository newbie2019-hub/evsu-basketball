<template>
  <div>
    <q-layout class="q-my-none">
      <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
          <q-list class="q-mt-md" :class="{'sticky-menu' : screen.gt.xs }" >
            <q-item clickable v-ripple :active="personalSettings" @click.prevent="personalSettings = true">
              <q-item-section
                ><q-item-label>Personal Settings</q-item-label>
                <q-item-label caption
                  >Update your account information</q-item-label
                >
              </q-item-section>
            </q-item>
            <q-item v-if="user?.user_type == 'admin'" clickable v-ripple :active="!personalSettings" @click.prevent="personalSettings = false">
              <q-item-section>
                <q-item-label>Account Registration</q-item-label>
                <q-item-label caption>Year, Course</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
          <div class="q-pl-sm">
            <AccountSettings v-if="personalSettings"/>
            <AccountRegistration v-else />
          </div>
        </div>
      </div>
    </q-layout>
  </div>
</template>
<script setup>
import AccountSettings from './Settings/AccountSettings.vue'
import AccountRegistration from './Settings/AccountRegistration.vue'
import { useQuasar } from 'quasar';
import { ref } from 'vue'
import { useAuthStore } from 'src/stores/authentication';

const { user } = useAuthStore()
const personalSettings = ref(true)
const { screen } = useQuasar()
</script>
<style>
.sticky-menu {
  position: sticky;
  top: 70px;
}

</style>
