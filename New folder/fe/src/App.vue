<template>
  <router-view />
</template>

<script setup>
import { useQuasar } from 'quasar';
const $q = useQuasar()

$q.loading.show({
  delay: 400 // ms
})

$q.loading.hide()
</script>
