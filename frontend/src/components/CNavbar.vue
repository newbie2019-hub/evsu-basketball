<template>
  <nav class="shadow-sm shadow-gray-200 fixed top-0 right-0 h-12 flex justify-end items-center w-navbar px-4">
    <c-avatar :name="name" email="adrian@gmail.com" />
  </nav>
</template>
<script setup>
import CAvatar from './CAvatar.vue'
import { useAuthStore } from '@/stores/authentication'
import { computed, onMounted } from 'vue'

const { user } = useAuthStore()

const name = computed(() => user.value?.first_name + ' ' + user.value?.last_name)

</script>
<style>
.w-navbar {
  width: calc(100% - 12rem)
}
</style>