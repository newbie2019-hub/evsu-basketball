<template>
  <q-layout view="lHh LpR fFf" class="q-px-md">
    <q-header class="bg-white text-black" bordered="">
      <q-toolbar>
        <q-btn dense flat round icon="mdi-menu" @click="toggleLeftDrawer" />

        <q-toolbar-title class="flex items-center"
          ><p class="text-weight-medium q-mb-none" style="font-size: 1.1rem">{{ route.name }}</p></q-toolbar-title
        >
      </q-toolbar>
    </q-header>

    <q-drawer
      show-if-above
      v-model="leftDrawerOpen"
      side="left"
      behavior="desktop"
      bordered
      :width="200"
      :mini="miniState && screen.gt.sm"
      :overlay="screen.lt.sm"
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <q-scroll-area
        style="
          height: calc(100% - 150px);
          margin-top: 150px;
          border-right: 1px solid #ddd;
        "
      >
        <q-list padding>
          <q-item
            clickable
            :active="navroute.name == route.name"
            v-ripple
            :to="navroute.to"
            v-for="(navroute, i) in links"
            :key="i"
          >
            <q-item-section avatar>
              <q-icon :name="navroute.icon" size="20px" color="grey-5" />
            </q-item-section>

            <q-item-section> {{ navroute.name }} </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>

      <q-img
        class="absolute-top"
        src="../assets/images/bg-header.jpg"
        style="height: 150px"
      >
        <div v-if="!miniState" class="absolute-bottom bg-transparent">
          <q-avatar color="primary" size="56px" class="q-mb-sm">
            <img :src="`https://robohash.org/${user.id}`" />
          </q-avatar>
          <div class="text-weight-bold">
            {{ user.first_name }} {{ user.last_name }}
          </div>
          <div>
            <p class="text-caption q-mb-none">{{ user.email }}</p>
          </div>
        </div>
      </q-img>
      <div
        v-if="screen.lt.sm"
        class="q-mini-drawer-hide absolute"
        style="top: 100px; right: -14px"
      >
        <q-btn
          dense
          round
          unelevated
          color="primary"
          icon="mdi-arrow-left"
          @click="leftDrawerOpen = false"
        />
      </div>
    </q-drawer>

    <q-page-container>
      <div class="row items-center justify-center">
        <div class="col-12" style="max-width: 1760px">
          <router-view />
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from "vue";
import { onBeforeMount } from "vue";
import { useAuthStore } from "../stores/authentication";
import { useQuasar } from "quasar";
import { links } from "../router/route-links";
import { useRoute } from "vue-router";

const { screen } = useQuasar();
let { authUser, user } = useAuthStore();
const route = useRoute();
const leftDrawerOpen = ref(false);
const miniState = ref(false);

onBeforeMount(async () => {
  const { data } = await authUser();
  user = data.data.user;
  console.log("User Data: ");
});

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};
</script>
<style>
.q-item__section--side {
  padding-right: 0px !important;
  min-width: 35px;
}
a.q-item--active div.q-item__section--side i {
  color: var(--q-primary) !important;
}

a.q-item--active .q-item__section.q-item__section--main {
  font-weight: 600;
}
</style>
