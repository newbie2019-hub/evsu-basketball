<template>
  <button
    :disabled="loading"
    class="px-3 py-2 rounded-md duration-100 ease-in-out focus:ring-2 focus:ring-offset-2 h-100"
    :class="[
      { 'w-full block': block },
      `bg-${color}-600 hover:bg-${color}-700 focus:ring-${color}-700 disabled:bg-${color}-200`,
      { uppercase: capitalize },
      { 'cursor-progress': loading },
      textColor,
      fontSize,
    ]"
  >
    <span v-if="!loading">{{ label }}</span>
    <span v-else class="flex justify-center items-center -ml-6"><mdicon :size="22" name="loading" :spin="true" />&nbsp; {{ loadingText ?? label }}...</span>
  </button>
</template>
<script setup>
import { computed } from "vue";

const props = defineProps({
  label: {
    type: String,
    default: "",
  },
  color: {
    type: String,
    default: "primary",
  },
  size: {
    type: String,
    default: "default",
  },
  block: {
    type: Boolean,
    default: false,
  },
  capitalize: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  loadingText: {
    type: String,
    default: null
  },
  iconOnly: {
    type: Boolean,
    default: false
  }
});

const color = computed(() => {
  switch (props.color) {
    case "transparent":
      return "transparent";
    case "success":
      return "green";
    case "warning":
      return "orange";
    case "error":
      return "red";
    default:
      return "blue";
  }
});

const fontSize = computed(() => {
  switch (props.size) {
    case "sm":
      return "text-xs";
    case "lg":
      return "text-md";
    default:
      return "text-sm";
  }
});

const textColor = computed(() => {
  if (props.color !== "transparent") return "text-white";
});
</script>
