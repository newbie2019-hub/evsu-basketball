<template>
  <div>drill.</div>
</template>
<script setup>
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/authentication";
import { useToast } from "vue-toastification";
import { onMounted } from "vue";

const authUser = useAuthStore();
const route = useRoute();
const router = useRouter();
const toast = useToast();


console.log(route.query);
onMounted(async () => {
  const { status, data } = await authUser.verify(route.query.email);
  if (status == 200) {
    toast.success(data.msg);
    router.push('/login')
  }
});
</script>
