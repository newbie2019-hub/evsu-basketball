<template>
  <div class="flex gap-x-2 items-center">
    <div class="-mt-[6px]">
      <p class="text-sm text-right">{{ name }}</p>
      <p class="text-xs leading-[.5] text-right text-gray-400">{{ email }}</p>
    </div>
    <div
      class="bg-gray-400 rounded-full w-10 h-10 flex items-center justify-center"
    >
      <span class="text-center text-sm">{{ displayName }}</span>
    </div>
  </div>
</template>
<script setup>
import { computed } from "vue";

const props = defineProps({
  name: {
    type: String,
    default: "",
  },
  color: {
    type: String,
    default: "",
  },
  size: {
    type: String,
    default: "24",
  },
  email: {
    type: String,
    default: "",
  },
});

const userName = props.name.split(" ");
const displayName = computed(() => {
  if (userName.length === 2) {
    return userName[0][0] + userName[1][0];
  } else {
    return userName[0][0];
  }
});
</script>
