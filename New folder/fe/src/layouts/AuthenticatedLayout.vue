<template>
  <q-layout view="lHh LpR fFf" class="q-px-md">
    <q-header class="bg-white text-black" bordered="">
      <q-toolbar>
        <q-btn dense flat round icon="mdi-menu" @click="toggleLeftDrawer" />

        <q-toolbar-title class="flex items-center"
          ><p class="text-weight-medium q-mb-none" style="font-size: 1.1rem">
            {{ route.name }}
          </p></q-toolbar-title
        >
        <div class="q-pr-sm">
          <q-avatar size="32px" class="cursor-pointer">
            <img src="https://cdn.quasar.dev/img/avatar.png" />
            <q-menu>
              <div class="column justify-center items-center q-px-md">
                <q-avatar size="62px" class="cursor-pointer q-mt-md">
                  <img src="https://cdn.quasar.dev/img/avatar.png" />
                </q-avatar>
                <p class="q-mb-none q-mt-sm">
                  {{ user.first_name }} {{ user.last_name }}
                </p>
                <p class="text-caption ellipsis">{{ user.email }}</p>
              </div>
              <q-list style="min-width: 150px">
                <q-item clickable v-close-popup>
                  <q-item-section class="text-no-wrap"
                    >Account Settings</q-item-section
                  >
                </q-item>
                <q-separator />
                <q-item clickable v-close-popup @click.prevent="userLogout">
                  <q-item-section>Logout</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-avatar>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      show-if-above
      v-model="leftDrawerOpen"
      side="left"
      behavior="desktop"
      bordered
      :width="200"
      :mini="miniState && screen.gt.xs"
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
        <div v-if="profileShown" class="absolute-bottom bg-transparent">
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
      <div class="row items-center justify-center q-my-md">
        <div class="col-12">
          <router-view />
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onBeforeMount, computed, watch } from "vue";
import { useAuthStore } from "../stores/authentication";
import { useQuasar } from "quasar";
import { links } from "../router/route-links";
import { useRoute, useRouter } from "vue-router";
import Cookies from "js-cookie";

const { screen } = useQuasar();
let { authUser, logout, user } = useAuthStore();
const route = useRoute();
const router = useRouter();
const leftDrawerOpen = ref(false);
const miniState = ref(true);

onBeforeMount(async () => {
  const { data } = await authUser();
  user = data.data.user;
});

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

const profileShown = computed(() => {
  if (screen.lt.sm) {
    return true;
  }

  if (miniState.value) {
    return false;
  }

  return true;
});

const userLogout = async() => {
  const { status, data } = await logout();

  if (status === 200) {
    Cookies.remove("access_token");
    localStorage.removeItem("user_data");
    router.push("/login");
  }

}
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
