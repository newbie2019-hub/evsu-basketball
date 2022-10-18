<template>
  <div
    class="h-screen w-48 border-r-2 fixed border-gray-100 left-0 top-0 bg-white py-4"
  >
    <img
      src="@/assets/images/eagles_logo.png"
      alt=""
      class="w-24 mx-auto block"
    />
    <p class="text-center text-sm mt-4">Welcome, {{ user.value?.last_name }}</p>
    <div class="mt-4">
      <ul class="space-y-2">
        <li v-for="link in links" class="">
          <router-link :to="link.to" class="flex items-center gap-x-2 hover:bg-blue-500 hover:text-white px-1 py-2.5 text-sm transition duration-200"><mdicon :size="19" :name="link.icon" class="ml-6"/>{{ link.name }}</router-link>
        </li>
      </ul>
    </div>
    <div class="absolute bottom-6 w-full flex">
      <a
        @click.prevent="signOut"
        type="button"
        href=""
        class="text-center mx-auto hover:text-blue-600 flex items-center text-sm"
        ><mdicon :size="20" name="logout" />&nbsp;Logout</a
      >
    </div>
  </div>
</template>
<script setup>
import { useAuthStore } from "@/stores/authentication";
import Cookies from "js-cookie";
import { useRouter } from "vue-router";
const { logout, user } = useAuthStore();

const links = [
  {
    name: 'Dashboard',
    to: 'dashboard',
    icon: 'chart-bubble'
  },
  {
    name: 'Athletes',
    to: 'athletes',
    icon: 'account-circle'
  },
  {
    name: 'Schedules',
    to: 'schedules',
    icon: 'calendar'
  },
  {
    name: 'Performance',
    to: 'performance',
    icon: 'speedometer'
  },
  {
    name: 'Drills',
    to: 'drills',
    icon: 'basketball'
  },
  {
    name: 'Settings',
    to: 'settings',
    icon: 'cog-outline'
  },
]

const router = useRouter();

const signOut = async () => {
  const { status, data } = await logout();
  if (status === 200) {
    Cookies.remove("access_token");
    localStorage.removeItem("user_data");
    router.push("/login");
  }
};
</script>
<style>
.router-link-exact-active {
  color: rgb(59 130 246);
  font-weight: bold;
  position: relative;
}

.router-link-exact-active::before {
  content: '';
  position: absolute;
  left: 0;
  height: 100%;
  width: 4px;
  border-top-right-radius: 10px;
  border-bottom-right-radius: 10px;
  background-color: rgb(59 130 246);
}
</style>