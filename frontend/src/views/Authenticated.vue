<template>
  <c-navbar></c-navbar>
  <c-sidebar></c-sidebar>
  <div class="ml-56 mt-20 mr-8">
    <router-view />
  </div>
</template>

<script setup>
import { onBeforeMount } from 'vue'
import CNavbar from "../components/CNavbar.vue";
import CSidebar from "../components/CSidebar.vue";

import { useAuthStore } from '@/stores/authentication'
const { authUser, user } = useAuthStore()

onBeforeMount(async() => {
  const { data } = await authUser()
  console.log('data: ',  data)
  user.value = data.data.user
})
</script>
